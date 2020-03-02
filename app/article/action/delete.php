<?php
defined ( 'IN_TS' ) or die ( 'Access Denied.' );

$userid = aac ( 'user' )->isLogin ();

$articleid = intval ( $_GET ['articleid'] );

$strArticle = $new ['article']->find ( 'article', array (
    'articleid' => $articleid
) );

//普通用户不允许删除内容
if($TS_SITE['isallowdelete'] && $TS_USER ['isadmin'] == 0) tsNotice('系统不允许用户删除内容，请联系管理员删除！');


switch($ts){

    #删除文章
    case "":

        if ($strArticle ['userid'] == $userid || $TS_USER ['isadmin'] == 1) {

            if($strArticle['photo']){
                unlink('uploadfile/article/'.$strArticle['photo']);
                tsDimg($strArticle['photo'],'article','320','180',$strArticle['path']);
            }

            $new ['article']->delete ( 'article', array (
                'articleid' => $articleid
            ) );


            #删除评论
            $new ['article']->delete ( 'comment', array (
                'ptable'=>'article',
                'pkey'=>'articleid',
                'pid'=>$articleid,
            ));


            $new ['article']->delete ( 'article_recommend', array (
                'articleid' => $articleid
            ) );


            if($strArticle['isaudit']==0){
                // 对积分进行处理
                aac('user') -> doScore($TS_URL['app'], $TS_URL['ac'], $TS_URL['ts'],$strArticle ['userid']);
            }


        }

        tsNotice('删除成功','点击返回文章首页',tsUrl ( 'article' ));

        break;


    case "video":

        $videoid = intval($_GET['videoid']);

        if ($strArticle ['userid'] == $userid || $TS_USER ['isadmin'] == 1) {

            $new['article']->delete('article_video',array(
                'articleid'=>$articleid,
                'videoid'=>$videoid,
            ));

        }

        header('Location: '.tsUrl('article','show',array('id'=>$articleid)));

        break;

}