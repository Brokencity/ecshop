<?php

namespace App\Http\Controllers\Admin;

use App\category;
use App\Goods;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use function Symfony\Component\VarDumper\Tests\Caster\reflectionParameterFixture;

class ListController extends Controller
{
        //添加分类模块
        public function lists_cate(){
            //接收数据
            $catename=trim(request('catename'));
            $active=request('active');
            //dd($catename);
            if(request()->has('act')){
                //闪存数据
                request()->flash();
                //查询管理员列表
                $cates=Category::from('category as c1')
                    ->leftjoin('category as c2','c1.pid','=','c2.id')
                    ->select('c1.*','c2.catename as parent_name')
                    ->where(function($query)    use($catename){
                        //判断分类有没有传catename的值
                        if($catename!=null){
                            $query->where('c1.catename','like',"%{$catename}%");
                        }
                    })
                    ->where(function($query) use($active){
                        if($active!=0){
                            $query->where('c1.active','=',$active);
                        }
                    })
                    ->paginate(5);

                //分配数据并显示视图
                return view('Admin.category.lists_cate',compact('cates','catename','active'));

                }else {
                //查询一级分类
                $cates = category::where('pid', 0)->paginate();
            }
                //dd($cates);
                //判断有没有子分类
                foreach($cates as $k=>$v){
                    //查询分类路径的个数
                    $count = category::where('pid',$v->id)->count();
                    //将子分类的个数写入分类数组
                    $cates[$k]->child_num = $count;
                     }

               //dd($cates);

                return view('Admin.category.lists_cate',compact('catename', 'active','cates'));
            }
        //上架|下架商品
        public function active($path,$active){
            //操作名称
            $act=$active==1?'上架':'下架';
            //更新分类状态
            if(category::where('path',$path)->orWhere('path','like',"$path,%")->update(['active'=>$active])){
                return response()->json(['status'=>'ok','msg'=>$act.'成功','active'=>$active,'path'=>$path]);
            }else{
                return response()->json(['status'=>'error','msg'=>$act.'失败']);
            }
        }


    //根据id删除分类
    public function delete($path){
        if(category::where('path',$path)->orWhere('path','like',"$path,%")->delete()){
            return response()->json(['status'=>'ok','msg'=>'删除成功','path'=>$path]);
        }else{
            return response()->json(['status'=>'error','msg'=>'删除失败']);
        }

    }
    //根据id来查询子分类
    public function getSubCate($id){
        //查询顶级分类
        $cates=Category::from('category as c1')
            ->leftjoin('category as c2','c1.pid','=','c2.id')
            ->select('c1.*','c2.catename as parentname')
            ->where('c1.pid',$id)
            ->get();

        //判断分类有没有子分类
        foreach($cates as $k=>$v){
            //将子分类的个数写入分类数组
            $cates[$k]->child_num=Category::where('pid',$v->id)->count();
        }

        //分类的勾选状态
        $checked=request('checked');
        return view('admin.category.subcate',compact('cates','checked'));
    }

        public function add_cate($pid=0){
            if(request()->isMethod('post')){
                //dd(request());
                //闪存数据
                request()->flash();
                //上传数据验证

                $this->validate(request(),[
                   'catename'=>'required|unique:category'
                    ],[
                    'catename.required'=>'用户名不能为空',
                    'catename.unqiue'=>'用户名已存在'
                ]);
                //接收数据
                //dd(request());
                $data['catename']=trim(request('catename'));
                $data['pid']=$pid;
                //dd($data);
                $id=category::insertGetId($data);
                //dd($id);
                //如果上传有值
                if($id){
                    //如果上传的pid数据==0，则是一级分类
                    //上传的id等于自己的path
                    if($pid==0){
                        $path=$id;
                    }else{
                        //获取上级分类的path路径，然后拼接
                        //根据自己的分类的pid，查找自己的path，find主键查找
                        $parentCate=category::select('path')->find($pid);
                        //根据自己的获取的路径，根据，拼接上自己的id
                        $path=$parentCate->path.','.$id;
                    }
                    //dd($path);
                    //将获取好的路径插入到数据表中
                    if(Category::where('id',$id)->update(['path'=>$path])){
                        return back()->with(['status'=>'ok','msg'=>'添加成功','url'=>route('bg/category/lists_cate')]);
                    }else{
                        return back()->with(['status'=>'error','msg'=>'添加失败']);
                    }
                }

            }else{
                //根据路径显示加载页面
                if($pid!=0){
                    //根据pid来查找上级分类id
                    $parentCate=category::select('id','catename')->find($pid);
                }else{
                    $parentCate=new\stdClass();
                    $parentCate->id=$pid;
                    $parentCate->catename='一级分类';
                }
                //dd($pid);
                return view('Admin/category/add_cate',compact('parentCate'));


            }

        }
                //产品销量图
                public function getGoodsTop5(){
                    //查询数据表的详细信息  查询表中字段载入柱状图
                    $goods=Goods::select('goods_name','saled_num')
                        ->orderBy('saled_num','desc')
                        ->offset(0)
                        ->limit(6)
                        ->get();
                    //遍历goods表中的数据
                    foreach($goods as $k=>$v){
                        $goodsArr['x'][]=mb_substr($v->goods_name,0,13,'utf8');
                        $goodsArr['y'][]=$v->saled_num;
                    }
                    //dd($goodsArr);
                    return response()->json($goodsArr);
        }

}