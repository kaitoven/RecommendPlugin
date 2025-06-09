<?php 
/**
 * 网站推荐模板
 *
 * @package custom
 */
if (!defined("__TYPECHO_ROOT_DIR__")) {
    exit();
}

// 获取推荐项目数据
$db = Typecho_Db::get();
$prefix = $db->getPrefix();
$select = $db->select()->from($prefix . 'recommend_items');
$items = $db->fetchAll($select);

// 分页逻辑
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 9;
$total_items = count($items);
$total_pages = ceil($total_items / $items_per_page);
$start_index = ($page - 1) * $items_per_page;
$items_to_display = array_slice($items, $start_index, $items_per_page);
?>
<!DOCTYPE html>
<html lang="zh">
<?php $this->need("header.php"); ?>
<body class="jasmine-body">
<div class="jasmine-container grid grid-cols-12">
    <?php $this->need("component/sidebar-left.php"); ?>
    <div class="flex col-span-12 lg:col-span-8 flex-col border-x-2 border-stone-100 dark:border-neutral-600 lg:pt-0 lg:px-6 pb-10 px-3">
        <?php $this->need("component/menu.php"); ?>
        <div class="flex flex-col gap-y-12">
            <!-- 推荐工具和网站内容开始 -->
            <div class="recommendation-section">
                <h1 class="recommendation-title">WEB Recommendation</h1>
                <div class="recommendation-grid">
                    <!-- 使用 PHP 渲染推荐项目 -->
                    <?php foreach ($items_to_display as $item): ?>
                    <a href="<?php echo $item['url']; ?>" target="_blank" class="recommendation-card">
                        <h3><?php echo $item['name']; ?></h3>
                        <div class="recommendation-hover-content">
                            <h3><?php echo $item['name']; ?></h3>
                            <p><?php echo $item['description']; ?></p> <!-- 显示描述信息 -->
                        </div>
                        <span class="category"><?php echo $item['category']; ?></span> <!-- 显示类别信息 -->
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- 手动分页按钮，只在多于1页时显示 -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="page-link">‹</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="page-link">›</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <!-- 手动分页按钮结束 -->
            </div>
            <!-- 推荐工具和网站内容结束 -->
        </div>
    </div>
    <div class="hidden lg:col-span-3 lg:block" id="sidebar-right">
        <?php $this->need("component/sidebar.php"); ?>
    </div>
</div>
<?php $this->need("footer.php"); ?>

<style>
  .recommendation-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr)); /* 3列布局 */
    gap: 20px;
    margin: 20px;
  }
  .recommendation-title {
    font-weight: bold;
    text-align: center;
    margin-top: 50px;
    margin-bottom: 45px; /* 增加标题与卡片列表的间隔 */
    font-size: 1.6em;
  }
  .recommendation-card {
    position: relative;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center; /* 标题居中 */
    transition: all 0.3s ease-in-out;
    overflow: hidden; /* 保证悬停内容不溢出 */
    cursor: pointer;
  }

  /*.recommendation-card:hover {*/
  /*  transform: scale(1.05);*/
  /*}*/
  .recommendation-card:hover {
  transform: scale(1.05);
  background: linear-gradient(135deg, #FFD700, #FFCC00, #FFA500); /* 流金渐变 */
  background-size: 200% 200%;
  animation: goldFlow 2s linear infinite;
  color: white;
  transition: all 0.4s ease-in-out;
  }

  /* 动态流动效果 */
  @keyframes goldFlow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }


  .recommendation-card h3 {
    font-size: 1.5em; /* 调整主标题为大字体 */
    margin-bottom: 20px;
  }

  /* 悬停时显示的内容，居中 */
  .recommendation-hover-content {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6); /* 半透明背景 */
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center; /* 垂直居中 */
    align-items: center; /* 水平居中 */
    text-align: center; /* 内容居中 */
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
  }

  .recommendation-card:hover .recommendation-hover-content {
    opacity: 1; /* 悬停时显示内容 */
  }

  .recommendation-hover-content h3 {
    font-size: 1.2em;
    margin-bottom: 10px;
  }

  .recommendation-hover-content p {
    font-size: 0.6em;
    margin: 0;
  }

  /* 类别显示在右下角 */
  .category {
    position: absolute;
    bottom: 10px;
    right: 15px;
    font-size: 0.6em; /* 类别字体较小 */
    color: #888;
    margin-top: 20px; /* 确保类别与其他内容有一定的间距 */
  }


.recommendation-card:hover .category {
  opacity: 0;
  transition: opacity 0.3s ease-in-out;
}


  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
  }

  .page-link {
    background-color: #f0f0f0;
    color: #000;
    padding: 10px 15px;
    margin: 0 5px;
    border-radius: 5px;
    text-decoration: none;
    transition: background-color 0.3s ease;
  }

  .page-link:hover {
    background-color: #e0e0e0;
  }

  .page-link.active {
    background-color: #000;
    color: #fff;
  }

  @media (max-width: 1200px) {
    .recommendation-card {
      padding: 12px;
    }
    .recommendation-card h3 {
      font-size: 1.3em; /* 确保在较大屏幕上标题仍然显眼 */
    }
  }

  @media (max-width: 992px) {
    .recommendation-card {
      padding: 10px;
    }
    .recommendation-card h3 {
      font-size: 1.2em;
    }
  }

  @media (max-width: 768px) {
    .recommendation-card {
      padding: 8px;
    }
    .recommendation-card h3 {
      font-size: 1.1em;
    }
  }

  @media (max-width: 576px) {
    .recommendation-card {
      padding: 6px;
    }
    .recommendation-card h3 {
      font-size: 1em;
    }
  }

  body {
    overflow-x: hidden;
  }
</style>

</body>
</html>
