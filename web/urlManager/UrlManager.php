<?php
/**
 * Created by TXM.
 * User: TXM
 * Date: 14-12-7
 * Time: 下午8:03
 */
namespace tmf\web\urlManager;

abstract class UrlManager{
    public abstract function createUrl($controller,$action,$data = null); //创建相对URL
    public abstract function createAbsoluteUrl($controller,$action,$data = null);//创建绝对路径URL
    public abstract function analyseUrl();//分析URL得出控制器，行为，get参数
}