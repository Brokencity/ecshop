<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Goods;
use App\GoodsImage;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Intervention\Image\ImageManagerStatic;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Worksheet_Drawing;

class GoodsController extends Controller
{
    //商品添加
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function addGoods(){
        //判断用户是否是post提交
        if(request()->isMethod('post')){


            //将数据存入闪存  便于表单验证
            request()->flash();
            //表单验证
            $this->validate(request(),[
                'goods_keyword'=>'bail|required|max:30|',
                'goods_name'=>'bail|required|unique:goods|max:100',
                'thirdCate'=>'bail|required',
                'price'=>'bail|required|numeric',
                'goods_card'=>'bail|required',
                'store_num'=>'bail|required|numeric',
                'goods_texture'=>'bail|required|',
                'goods_weight'=>'bail|required|numeric',
                'diamond_num'=>'bail|required|numeric',
                'diamond_weight'=>'bail|required|numeric',
                'details'=>'bail|required|max:255',
                'diamond_color'=>['bail','regex:/^[A-Z]/'],
                'cleanliness'=>['bail','regex:/^[A-Z]/'],
                'diamond_cut'=>['bail','regex:/^[A-Z]/'],
                'image'=>'bail|required'
                ],[
                'goods_keyword.required'=>'商品关键字子不能为空',
                'goods_keyword.max'=>'商品关键字子不能超过30个字',
                'goods_name.required'=>'商品名称不能为空',
                'goods_name.unique'=>'商品名称已被占用，请重新填写',
                'goods_name.max'=>'商品名称不能超过100个字符',
                'thirdCate.required'=>'必须选则商品分类',
                'price.required'=>'商品价格不能为空',
                'price.numeric'=>'商品价格只能填写数字',
                'goods_card.required'=>'商品编号不能为空',
                'store_num.required'=>'库存数量不能为空',
                'store_num.numeric'=>'库存数量只能填写数字',
                'goods_texture.required'=>'商品材质不能为空',
                'goods_weight.required'=>'商品重量不能为空',
                'goods_weight.numeric'=>'商品重量只能填写数字',
                'diamond_num.required'=>'钻石数量不能为空',
                'diamond_num.numeric'=>'钻石数量只能填写数字',
                'diamond_weight.required'=>'钻石重量不能为空',
                'diamond_weight.numeric'=>'钻石重量只能填写数字',
                'details.required'=>'商品详情不能为空',
                'details.max'=>'商品详情不能超过255个字符',
                'diamond_color.regex'=>'请选择钻石颜色',
                'diamond_num.regex'=>'请选择钻石颜色',
                'cleanliness.regex'=>'请选择钻石净度',
                'diamond_cut.regex'=>'请选择钻石切工',
                'image.required'=>'商品主图不能为空'
                ]);
                //业务逻辑
               //接受数据
                $data=request()->only('goods_card','goods_keyword','goods_name','price','store_num','goods_card','goods_weight','diamond_num','diamond_weight','details','diamond_color','cleanliness','diamond_cut','goods_texture');
                $data['cid']=request('thirdCate');
                $data['add_time']=time();

                //主图上传
            if(request()->hasFile('image')){
                if($imageInfo=$this->upload(request()->file('image'))){
                    $data['image_dir']=$imageInfo['img_dir'];
                    $data['image']=$imageInfo['img_name'];

                    if($gid=Goods::insertGetId($data)){

                        //写入session
                        Session::put('lastGoodsId',$gid);
                        return response()->json(['status'=>'ok','msg'=>'商品发布成功']);
                        }else{
                            return response()->json(['status'=>'error','msg'=>'商品发布失败']);
                        }
                    }else{
                        return response()->json(['status'=>'error','msg'=>'商品发布失败']);
                        }
                    }else{
                        return response()->json(['status'=>'error','msg'=>'请选择商品主图']);
                    }

                }else{
                    //不是就显示添加页面
                    return view('Admin.goods.add');
                }
    }

    //商品图片上传处理
    public function addImage(){
        if(request()->hasFile('file')){
            if($imageInfo=$this->upload(request()->file('file'))){
                $data['gid']=Session::get('lastGoodsId');
                $data['image_dir']=$imageInfo['img_dir'];
                $data['image']=$imageInfo['img_name'];
                GoodsImage::insert($data);
            }
        }
    }

    //图片上传
    public function upload($pic)
    {
        //获取扩展名
        $ext = $pic->getClientOriginalExtension();
        //目标文件名称
        $name = date('YmdHis') . '-' . uniqid() . '.' . $ext;
        //保存的目录
        $dir = config('filesystems.upload');
        //保存
        if ($pic->move($dir, $name)) {
            //缩放
            $img1 = ImageManagerStatic::make($dir . $name)->resize(400, 400)->save($dir . '400_' . $name);
            $img2 = ImageManagerStatic::make($dir . $name)->resize(240, 240)->save($dir . '240_' . $name);
            $img3 = ImageManagerStatic::make($dir . $name)->resize(100, 100)->save($dir . '100_' . $name);

            //销毁
            $img1->destroy();
            $img2->destroy();
            $img3->destroy();

            //返回
            return ['img_dir' => $dir, 'img_name' => $name];
        } else {
            return false;
        }
    }

    //商品列表
    public function goodsList(){
        //接收表单数据
        $goodsname=trim(request('goodsname'));
        $active=request('active');
        $start_time=request('start_time');
        $end_time=request('end_time');
        //保持数据
        request()->flash();
        //查询数据
        $goods=Goods::where(function($query) use($goodsname){
                        //判断没有有传商品关键字
                        if($goodsname!==null){
                                $query->where('goods_name','like',"%$goodsname%");
                            }
                        })
                       ->where(function ($query) use($active){
                           //判断没有没传状态值
                           if($active>0){
                               $query->where('active',$active);
                           }
                       })
                        ->where(function($query) use($start_time,$end_time){
                            //只传了起始时间，没有传结束时间
                            if($start_time && !$end_time){
                                $query->where('add_time','>=',strtotime($start_time));
                            }else if($end_time && !$start_time){
                                //只传了结束时间，没有传起始时间
                                $query->where('add_time','<=',strtotime($end_time));
                            }else if($start_time && $end_time){
                                //只传了起始时间，没有结束传时间
                                $query->whereBetween('add_time',[strtotime($start_time),strtotime($end_time)]);
                            }
                        })

                        ->orderBy('id','desc')
                    ->paginate(5);//每页显示5条
        return view('Admin.goods.list',compact('goods','goodsname','active','start_time','end_time'));
    }

    //通过id修改商品状态
    public function goodsActive($id,$active){

           $act=$active==1?'上架':'下架';
            if(Goods::where('id',$id)->update(['active'=>$active])){
                return response()->json(['status'=>'ok','msg'=>$act.'成功','id'=>$id,'active'=>$active]);
            }else{
                return response()->json(['status'=>'error','msg'=>$act.'失败']);
            }
    }

    //通过id删除商品
    public function goodsDelete($id){
            if(Goods::destroy($id)){
                return response()->json(['status'=>'ok','msg'=>'删除成功']);
            }else{
                return response()->json(['status'=>'error','msg'=>'删除失败']);
            }
    }

    //批量删除商品
    public function goodsDeletes(){
        if(Goods::destroy(request('chk'))){
            return response()->json(['status'=>'ok','msg'=>'删除成功']);
        }else{
            return response()->json(['status'=>'error','msg'=>'删除失败']);
        }
    }

    //编辑商品
    public function goodsRedact($id){

        if(request()->isMethod('post')){
            $this->validate(request(),[
                'goods_keyword'=>'bail|required|max:30|',
                'goods_name'=>'bail|required|max:100|unique:goods,goods_name,'.$id,
                'thirdCate'=>'bail|required',
                'price'=>'bail|required|numeric',
                'goods_card'=>'bail|required',
                'store_num'=>'bail|required|numeric',
                'goods_texture'=>'bail|required|',
                'goods_weight'=>'bail|required|numeric',
                'diamond_num'=>'bail|required|numeric',
                'diamond_weight'=>'bail|required|numeric',
                'details'=>'bail|required|max:255',
                'diamond_color'=>['bail','regex:/^[A-Z]/'],
                'cleanliness'=>['bail','regex:/^[A-Z]/'],
                'diamond_cut'=>['bail','regex:/^[A-Z]/'],
                'image'=>'bail|required'
            ],[
                'goods_keyword.required'=>'商品关键字子不能为空',
                'goods_keyword.max'=>'商品关键字子不能超过30个字',
                'goods_name.required'=>'商品名称不能为空',
                'goods_name.unique'=>'商品名称已被占用，请重新填写',
                'goods_name.max'=>'商品名称不能超过100个字符',
                'thirdCate.required'=>'必须现则商品分类',
                'price.required'=>'商品价格不能为空',
                'price.numeric'=>'商品价格只能填写数字',
                'goods_card.required'=>'商品编号不能为空',
                'store_num.required'=>'库存数量不能为空',
                'store_num.numeric'=>'库存数量只能填写数字',
                'goods_texture.required'=>'商品材质不能为空',
                'goods_weight.required'=>'商品重量不能为空',
                'goods_weight.numeric'=>'商品重量只能填写数字',
                'diamond_num.required'=>'钻石数量不能为空',
                'diamond_num.numeric'=>'钻石数量只能填写数字',
                'diamond_weight.required'=>'钻石重量不能为空',
                'diamond_weight.numeric'=>'钻石重量只能填写数字',
                'details.required'=>'商品详情不能为空',
                'details.max'=>'商品详情不能超过255个字符',
                'diamond_color.regex'=>'请选择钻石颜色',
                'diamond_num.regex'=>'请选择钻石颜色',
                'cleanliness.regex'=>'请选择钻石净度',
                'diamond_cut.regex'=>'请选择钻石切工',
                'image.required'=>'商品主图不能为空'
            ]);
            //业务逻辑
            //接受数据
            $data=request()->only('goods_card','goods_keyword','goods_name','price','store_num','goods_card','goods_weight','diamond_num','diamond_weight','details','diamond_color','cleanliness','diamond_cut','goods_texture');
            $data['cid']=request('thirdCate');
            $data['add_time']=time();
            //图片修改


            if(request()->hasFile('image')){
                foreach (request()->file('image') as $k=>$image) {
                    if ($goodsImages = $this->upload($image)) {
                        //删除旧图
                        if ($k == 0) {
                            $data['image_dir'] = $goodsImages['img_dir'];
                            $data['image'] = $goodsImages['img_name'];
                            //下标为0，代表修改主图
                            $goodsInfo = Goods::select('image_dir', 'image')->find($id);
                        } else {
                            $dataImg['image_dir'] = $goodsImages['img_dir'];
                            $dataImg['image'] = $goodsImages['img_name'];
                            $goodsInfo = GoodsImage::select('image_dir', 'image')->find($k);
                            //更新副表的图片信息
                            if(!GoodsImage::where('id',$k)->update($dataImg)){
                                return response()->json(['status'=>'error','msg'=>'商品图片更新失败']);
                            }
                        }
                        unlink($goodsInfo->image_dir.$goodsInfo ->image);
                        unlink($goodsInfo->image_dir . '400_' . $goodsInfo ->image);
                        unlink($goodsInfo->image_dir . '240_' . $goodsInfo ->image);
                        unlink($goodsInfo->image_dir . '100_' . $goodsInfo ->image);

                    }
                }
            }
            //更新新添加的商品图片
            if(request()->hasFile('newimg')){
                foreach(request()->file('newimg') as $file){
                    if($imageInfo=$this->upload($file)){
                        $data1['gid']=$id;
                        $data1['image_dir']=$imageInfo['img_dir'];
                        $data1['image']=$imageInfo['img_name'];
                        GoodsImage::insert($data1);
                    }
                }
            }
            //更新商品主表
            if(Goods::where('id',$id)->update($data)){
                return response()->json(['status'=>'ok','msg'=>'商品更新成功']);
            }else{
                return response()->json(['status'=>'error','msg'=>'商品更新失败']);
            }
        }else{
            //获取商品信息
            $goods=Goods::find($id);
            //获取商品图片信息
            $goodsImage=GoodsImage::where('gid',$id)->get();
            //获取商品分类信息
            $cate=category::select('path')->find($goods->cid);
            $cates=category::select('id','catename')->find(explode(',',$cate->path));
            return view('Admin.goods.redact',compact('goods','goodsImage','cates'));

        }
    }
    //

}
