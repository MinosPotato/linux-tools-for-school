<?php
session_start();
  
require_once 'private/config.php';
  
$db_path = 'private/data/tools.db';
  
if (isset($_POST['login'])) {
    if (password_verify($_POST['password'], $admin_password_hash)) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "Wrong Password";
    }
}
  
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}
  
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Login</title>
        <link rel="stylesheet" href="style/admin.css">
    </head>
    <body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.html" class="logo">LINUX FOR SCHOOL</a>
            <div class="nav-links">
                <a href="index.html">Home</a>
                <a href="tools.php">All Tools</a>
                <a href="about.html">About</a>
            </div>
            <a href="https://github.com/MinosPotato" class="btn-github">My GitHub</a>
        </div>
    </nav>
        <div class="login-box">
            <h2>Admin Access</h2>
            <?php if(isset($error)) echo "<p class='error-text'>$error</p>"; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter Password" required><br>
                <button type="submit" name="login" class="btn-submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}
  
try {
    $db = new PDO("sqlite:$db_path");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
  
if (isset($_GET['delete_tool'])) {
    $stmt = $db->prepare("DELETE FROM tools WHERE id = ?");
    $stmt->execute([$_GET['delete_tool']]);
    header("Location: admin.php");
    exit;
}
  
if (isset($_GET['delete_cat'])) {
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$_GET['delete_cat']]);
    header("Location: admin.php");
    exit;
}
  
if (isset($_POST['add_category'])) {
    $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$_POST['cat_name']]);
    header("Location: admin.php");
    exit;
}
  
if (isset($_POST['add_tool'])) {
    $stmt = $db->prepare("INSERT INTO tools (name, url, description) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['url'], $_POST['description']]);
    $tool_id = $db->lastInsertId();
  
    if (!empty($_POST['categories'])) {
        foreach ($_POST['categories'] as $cat_id) {
            $stmt = $db->prepare("INSERT INTO tool_categories (tool_id, category_id) VALUES (?, ?)");
            $stmt->execute([$tool_id, $cat_id]);
        }
    }
    header("Location: admin.php");
    exit;
}
  
$tools = $db->query("SELECT * FROM tools ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Tool Manager</title>
    <link rel="stylesheet" href="style/admin.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="./" class="logo">LINUX FOR SCHOOL</a>
            <div class="nav-links">
                <a href="/">Home</a>
                <a href="tools.php">All Tools</a>
                <a href="about.html">About</a>
            </div>
            <a href="?logout=1" class="btn-logout">Logout</a>
        </div>
    </nav>
  
    <div class="admin-container">
        <div class="header">
            <h1>Tool Administration</h1>
        </div>
  
        <div class="flex-grid">
            <div class="panel">
                <h3>Add New Tool</h3>
                <form method="POST">
                    <input type="text" name="name" placeholder="Tool Name" required>
                    <input type="url" name="url" placeholder="https://example.com" required>
                    <textarea name="description" placeholder="Short description..."></textarea>
                    <label class="section-label">Assign Categories:</label>
                    <div class="cat-checkboxes">
                        <?php foreach ($categories as $cat): ?>
                            <label>
                                <input type="checkbox" name="categories[]" value="<?php echo $cat['id']; ?>" class="checkbox-input">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" name="add_tool" class="btn-submit btn-tool">Add Tool</button>
                </form>
            </div>
  
            <div class="panel">
                <h3>Manage Categories</h3>
                <form method="POST" class="cat-form">
                    <input type="text" name="cat_name" placeholder="New Category Name" required>
                    <button type="submit" name="add_category" class="btn-submit btn-category">Create Category</button>
                </form>
                <table>
                    <thead><tr><th>Name</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td><b><a href="?delete_cat=<?php echo $cat['id']; ?>" class="btn-delete" onclick="return confirm('Delete this category?')">Delete</a></b></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
  
        <h3>Existing Tools</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tools as $tool): ?>
                <tr>
                    <td><b><?php echo htmlspecialchars($tool['name']); ?></b></td>
                    <td><b><?php echo htmlspecialchars($tool['url']); ?></b></td>
                    <td><b>
                        <a href="?delete_tool=<?php echo $tool['id']; ?>"
                           class="btn-delete"
                           onclick="return confirm('Are you sure?')">Delete</a>
                    </b></td>
                </tr>
                <tr><td colspan="3"><?php echo htmlspecialchars($tool['description']); ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>