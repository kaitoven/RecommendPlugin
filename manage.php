<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

include 'header.php';
include 'menu.php';

$db = Typecho_Db::get();
$prefix = $db->getPrefix();
$message = '';

$itemsPerPage = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $itemsPerPage;

// 表单处理
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $db->query($db->delete($prefix . 'recommend_items')->where('id = ?', $id));
        $message = '项目删除成功！';
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $url = $_POST['url'];
        $db->query($db->update($prefix . 'recommend_items')->rows([
            'name' => $name,
            'category' => $category,
            'description' => $description,
            'url' => $url
        ])->where('id = ?', $id));
        $message = '项目修改成功！';
    } else {
        $name = $_POST['name'];
        $category = $_POST['category'];
        $description = $_POST['description'];
        $url = $_POST['url'];
        $db->query($db->insert($prefix . 'recommend_items')->rows([
            'name' => $name,
            'category' => $category,
            'description' => $description,
            'url' => $url
        ]));
        $message = '项目添加成功！';
    }
}

$totalItems = $db->fetchObject($db->select(['COUNT(*)' => 'total'])->from($prefix . 'recommend_items'))->total;
$totalPages = ceil($totalItems / $itemsPerPage);

$items = $db->fetchAll(
    $db->select()->from($prefix . 'recommend_items')->offset($offset)->limit($itemsPerPage)
);

$editItem = null;
if (isset($_GET['edit_id'])) {
    $editId = $_GET['edit_id'];
    $editItem = $db->fetchRow(
        $db->select()->from($prefix . 'recommend_items')->where('id = ?', $editId)
    );
}
?>

<div class="main">

<?php if ($message): ?>
    <div class="alert success">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<style>
    .add-project-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        max-width: 600px;
    }
    .add-project-card h3 {
        margin-top: 0;
        margin-bottom: 1rem;
        font-size: 1.3rem;
    }
    .add-project-card label {
        font-weight: bold;
        display: block;
        margin-bottom: 0.3rem;
    }
    .add-project-card input[type="text"],
    .add-project-card input[type="url"],
    .add-project-card textarea {
        width: 100%;
        padding: 0.6rem;
        margin-bottom: 1rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 0.95rem;
    }
    .add-project-card textarea {
        resize: vertical;
        min-height: 80px;
    }
    .add-project-card button {
        background-color: #28a745;
        color: white;
        padding: 0.6rem 1.2rem;
        border: none;
        border-radius: 4px;
        font-size: 0.95rem;
        cursor: pointer;
    }
    .add-project-card button:hover {
        background-color: #218838;
    }
    .recommend-card-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    .recommend-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.3s ease;
    }
    .recommend-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        background: linear-gradient(to right, #f7e9be, #fff1c1);
    }
    .recommend-card h4 {
        margin: 0 0 0.5rem;
    }
    .recommend-card .category {
        font-size: 0.85rem;
        color: #888;
        margin-bottom: 0.5rem;
    }
    .recommend-card .description {
        flex-grow: 1;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
    }
    .recommend-card .actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }
    .recommend-card .actions a,
    .recommend-card .actions button {
        font-size: 0.85rem;
        padding: 0.3rem 0.6rem;
        border: none;
        border-radius: 4px;
        text-decoration: none;
        color: white;
        background-color: #007bff;
        cursor: pointer;
    }
    .recommend-card .actions a:hover,
    .recommend-card .actions button:hover {
        background-color: #0056b3;
    }
    .recommend-card .actions .danger {
        background-color: #dc3545;
    }
    .recommend-card .actions .danger:hover {
        background-color: #a71d2a;
    }
    /* New Pagination Style */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    .page-link {
        background-color: #f0f0f0;
        color: #000;
        padding: 8px 14px;
        margin: 0 5px;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
        font-size: 0.95rem;
    }
    .page-link:hover {
        background-color: #e0e0e0;
    }
    .page-link.active {
        background-color: #000;
        color: #fff;
    }
</style>

<div class="add-project-card">
    <h3><?php echo isset($editItem) ? '✏️ 修改推荐项目' : '➕ 添加推荐项目'; ?></h3>
    <form method="post">
        <?php if (isset($editItem)): ?>
            <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
        <?php endif; ?>
        <label>项目名称：</label>
        <input type="text" name="name" value="<?php echo isset($editItem) ? $editItem['name'] : ''; ?>" required>
        <label>类别：</label>
        <input type="text" name="category" value="<?php echo isset($editItem) ? $editItem['category'] : ''; ?>" required>
        <label>简短说明：</label>
        <textarea name="description"><?php echo isset($editItem) ? $editItem['description'] : ''; ?></textarea>
        <label>项目网址：</label>
        <input type="url" name="url" value="<?php echo isset($editItem) ? $editItem['url'] : ''; ?>" required>
        <button type="submit" name="<?php echo isset($editItem) ? 'edit' : 'submit'; ?>">
            <?php echo isset($editItem) ? '更新项目' : '添加项目'; ?>
        </button>
    </form>
</div>

<hr>

<h3>📋 已添加的推荐项目</h3>
<div class="recommend-card-container">
    <?php foreach ($items as $item): ?>
        <div class="recommend-card">
            <h4><?php echo htmlspecialchars($item['name']); ?></h4>
            <div class="category">📂 <?php echo htmlspecialchars($item['category']); ?></div>
            <div class="description"><?php echo htmlspecialchars($item['description']); ?></div>
            <div class="actions">
                <a href="<?php echo htmlspecialchars($item['url']); ?>" target="_blank">🔗 访问</a>
                <div>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <button type="submit" name="delete" class="danger" onclick="return confirm('确定删除此项目？');">删除</button>
                    </form>
                    <a href="<?php echo $options->adminUrl('extending.php?panel=RecommendPlugin/manage.php&edit_id=' . $item['id']); ?>">编辑</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($items)): ?>
        <p>暂无推荐项目。</p>
    <?php endif; ?>
</div>

<!-- 分页 -->
<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="<?php echo $options->adminUrl('extending.php?panel=RecommendPlugin/manage.php&page=' . ($page - 1)); ?>" class="page-link">‹</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="<?php echo $options->adminUrl('extending.php?panel=RecommendPlugin/manage.php&page=' . $i); ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="<?php echo $options->adminUrl('extending.php?panel=RecommendPlugin/manage.php&page=' . ($page + 1)); ?>" class="page-link">›</a>
    <?php endif; ?>
</div>
<?php endif; ?>


</div>

<?php include 'footer.php'; ?>
