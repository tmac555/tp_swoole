<?php
// 应用公共文件
function show($status,$msg='',$data=[]){
    $result=[
        'status'=>$status,
        'msg'=>$msg,
        'data'=>$data
    ];
    return json($result);

}