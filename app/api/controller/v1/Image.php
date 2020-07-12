<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/10 0010
 * Time: 17:41
 */

namespace app\api\controller\v1;

use app\BaseController;
class Image extends BaseController
{
   public function upload(){

       $file = request()->file('file');
       // 上传到本地服务器
       $savename = \think\facade\Filesystem::disk('public')->putFile( 'live', $file);
        if($savename){
             return show(1,'upload success',$savename);
        }else{
            return show(0,'upload error');
        }


   }
}