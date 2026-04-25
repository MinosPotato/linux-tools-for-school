<?php
try {
    $db = new PDO('sqlite:./tools.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 1. Fetch tools and a COMMA-SEPARATED list of their categories
$toolQuery = "SELECT tools.name, tools.url, tools.description, 
              GROUP_CONCAT(categories.name, ', ') as category_list
              FROM tools 
              JOIN tool_categories ON tools.id = tool_categories.tool_id
              JOIN categories ON tool_categories.category_id = categories.id
              GROUP BY tools.id";
$tools = $db->query($toolQuery)->fetchAll(PDO::FETCH_ASSOC);

// 2. Fetch all categories for the dropdown
$catQuery = "SELECT * FROM categories ORDER BY name ASC";
$allCategories = $db->query($catQuery)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tools - LinuxToolbox</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <span class="gradient">LINUX</span>FORSCHOOL
            </div>
            <div class="nav-links">
                <a href="./">Home</a>
                <a href="tools.html">All Tools</a>
            </div>
            <a href="https://github.com/MinosPotato" class="btn-tools">My GitHub</a>
        </div>
    </nav>

    <section class="tools-header">
        <div class="container">
            <h1>Tool <span class="kali-gradient">Directory</span></h1>
            <p class="hero-subtitle">Instant search through the essential utilities for Linux.</p>
            <div class="filter-group">
                <input type="text" id="searchInput" class="filter-input" placeholder="Search tools instantly...">
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
                                foreach($tags as $tag) {
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

    <footer class="footer">&copy; 2023 LinuxToolbox. Built for the community.</footer>

    <script>
        const searchInput = document.getElementById('searchInput');
        const categorySelect = document.getElementById('categorySelect');
        const toolItems = document.querySelectorAll('.tool-item');

        function filterTools() {
            const searchText = searchInput.value.toLowerCase();
            const selectedCat = categorySelect.value;

            toolItems.forEach(item => {
                const name = item.querySelector('.tool-name').textContent.toLowerCase();
                const desc = item.querySelector('.tool-desc').textContent.toLowerCase();
                const categories = item.getAttribute('data-categories').toLowerCase();

                const matchesSearch = name.includes(searchText) || desc.includes(searchText);
                const matchesCategory = (selectedCat === 'all' || categories.includes(selectedCat.toLowerCase()));

                item.style.display = (matchesSearch && matchesCategory) ? 'flex' : 'none';
            });
        }

        searchInput.addEventListener('input', filterTools);
        categorySelect.addEventListener('change', filterTools);
    </script>
</body>
</html>