<?php
try {
    $pdo = new PDO('sqlite:private/data/tools.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

try {
    $stmt = $pdo->query("SELECT tools.name, tools.url, tools.description,
            GROUP_CONCAT(categories.name, ', ') as category_list
            FROM tools
            JOIN tool_categories ON tools.id = tool_categories.tool_id
            JOIN categories ON tool_categories.category_id = categories.id
            GROUP BY tools.id");
    $tools = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database query failed: " . $e->getMessage());
}

try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    $allCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database category list query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tools - Linux for school</title>
    <link rel="stylesheet" href="style/style.css">
</head>

<body>

        <nav class="navbar">
            <div class="nav-container">
                <a href="index.html" class="logo">LINUX FOR SCHOOL</a>
                <div class="nav-links">
                    <a href="index.html">Home</a>
                    <a href="tools.php">All Tools</a>
                </div>
                <a href="https://github.com/MinosPotato" class="btn-github">My GitHub</a>
            </div>
        </nav>

    <section class="tools-header">
        <div class="container">
            <h1>Tool Directory</h1>
            <div class="filter-group">
                <input type="text" id="searchInput" class="filter-input" placeholder="Search tools..">
                <select id="categorySelect" class="filter-select">
                    <option value="all">All Categories</option>
                    <?php foreach ($allCategories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['name']); ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </section>

    <section class="container">
        <div class="tool-list" id="toolList">
            <?php if (count($tools) > 0): ?>
                <?php foreach ($tools as $tool): ?>
                    <a href="<?php echo htmlspecialchars($tool['url']); ?>"
                        target="_blank" class="tool-item"
                        data-categories="<?php echo htmlspecialchars($tool['category_list']); ?>">
                        <div class="tool-info">
                            <h3 class="tool-name"><?php echo htmlspecialchars($tool['name']); ?></h3>
                            <p class="tool-desc"><?php echo htmlspecialchars($tool['description']); ?></p>
                        </div>
                        <div class="tool-tags">
                            <?php

                            $tags = explode(', ', $tool['category_list']);
                            foreach ($tags as $tag) {
                                echo "<span class='category-tag'>" . htmlspecialchars($tag) . "</span>";
                            }
                            ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #94a3b8; margin-bottom: 80px;">No tools found.</p>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        Built as a school project.
    </footer>

    <script src="script.js"></script>
</body>

</html>
