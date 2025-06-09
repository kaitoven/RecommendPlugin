<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 网站和工具推荐后台管理插件，启用后管理处会出现网站推荐选项
 * 前端展示是作为独立页面生成的,只需要新增独立页面并选择"网站推荐模板"即可。前端页面请参照page-recommend.php
 * 
 * @package RecommendPlugin
 * @author Kaitoven Chen
 * @version 1.0.1
 * @link https://www.chendk.info
 */
class RecommendPlugin_Plugin implements Typecho_Plugin_Interface
{
    // 激活插件时创建推荐项目的数据库表
    public static function activate()
    {
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        
        // 检查是否已有 url 列，如果没有，则添加
        try {
            $db->query("SELECT `url` FROM `{$prefix}recommend_items` LIMIT 1");
        } catch (Exception $e) {
            // 添加 url 列
            $db->query("ALTER TABLE `{$prefix}recommend_items` ADD `url` VARCHAR(255) NOT NULL AFTER `description`");
        }
    
        Helper::addPanel(3, 'RecommendPlugin/manage.php', '网站推荐', '管理推荐项目', 'administrator');
        
        return _t('推荐插件已激活');
    }


    // 禁用插件时删除管理页面入口
    public static function deactivate()
    {
        Helper::removePanel(3, 'RecommendPlugin/manage.php');
        return _t('推荐插件已禁用');
    }

    // 插件配置
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $title = new Typecho_Widget_Helper_Form_Element_Text('title', NULL, '网站推荐', _t('推荐页面标题'));
        $form->addInput($title);
    }

    // 个人配置（暂时不需要）
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    // 渲染推荐内容的方法（供前端模板调用）
    public static function getRecommendations()
    {
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        $select = $db->select()->from($prefix . 'recommend_items');
        return $db->fetchAll($select);
    }
}
?>
