<?php
/**
 * API Backend for Om Yaman Spices (توابل وبهارات أم يمان)
 * Full-Stack E-commerce RESTful API
 */

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Data file paths
$dataPath = __DIR__ . '/data';
$productsFile = $dataPath . '/products.json';
$categoriesFile = $dataPath . '/categories.json';

// Admin credentials
define('ADMIN_USER', 'om7758217');
define('ADMIN_PASS', '7758217om');

// Helper to send json response
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

// Helper to read JSON
function readJsonFile($file, $default = []) {
    if (!file_exists($file)) {
        return $default;
    }
    $content = file_get_contents($file);
    $decoded = json_decode($content, true);
    return is_array($decoded) ? $decoded : $default;
}

// Helper to write JSON safely
function writeJsonFile($file, $data) {
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if (!is_dir(dirname($file))) {
        mkdir(dirname($file), 0777, true);
    }
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

// Auth check
function isAdminLoggedIn() {
    return isset($_SESSION['om_yaman_admin']) && $_SESSION['om_yaman_admin'] === true;
}

// Get action
$action = isset($_GET['action']) ? trim($_GET['action']) : '';

// Handle actions
switch ($action) {

    // 1. Get Products with filtering, search, and pagination
    case 'get_products':
        $products = readJsonFile($productsFile, []);
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';
        $featured = isset($_GET['featured']) ? trim($_GET['featured']) : '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0; // 0 means all

        // Filter by category
        if (!empty($category) && $category !== 'all') {
            $products = array_values(array_filter($products, function($p) use ($category) {
                return isset($p['category']) && $p['category'] === $category;
            }));
        }

        // Filter by featured
        if ($featured === '1' || $featured === 'true') {
            $products = array_values(array_filter($products, function($p) {
                return !empty($p['featured']);
            }));
        }

        // Filter by search query
        if (!empty($search)) {
            $searchLower = mb_strtolower($search, 'UTF-8');
            $products = array_values(array_filter($products, function($p) use ($searchLower) {
                $name = mb_strtolower($p['name'] ?? '', 'UTF-8');
                $desc = mb_strtolower($p['description'] ?? '', 'UTF-8');
                $sku = mb_strtolower($p['sku'] ?? '', 'UTF-8');
                return strpos($name, $searchLower) !== false || 
                       strpos($desc, $searchLower) !== false || 
                       strpos($sku, $searchLower) !== false;
            }));
        }

        // Sorting
        if ($sort === 'price_asc') {
            usort($products, function($a, $b) { return $a['price'] <=> $b['price']; });
        } elseif ($sort === 'price_desc') {
            usort($products, function($a, $b) { return $b['price'] <=> $a['price']; });
        } elseif ($sort === 'name') {
            usort($products, function($a, $b) { return strcmp($a['name'], $b['name']); });
        }

        $totalItems = count($products);

        // Pagination
        if ($limit > 0) {
            $offset = ($page - 1) * $limit;
            $paginatedProducts = array_slice($products, $offset, $limit);
            jsonResponse([
                'success' => true,
                'total' => $totalItems,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($totalItems / $limit),
                'products' => $paginatedProducts
            ]);
        } else {
            jsonResponse([
                'success' => true,
                'total' => $totalItems,
                'products' => $products
            ]);
        }
        break;

    // 2. Get Single Product
    case 'get_product':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $products = readJsonFile($productsFile, []);
        foreach ($products as $p) {
            if ($p['id'] === $id) {
                jsonResponse(['success' => true, 'product' => $p]);
            }
        }
        jsonResponse(['success' => false, 'message' => 'المنتج غير موجود'], 404);
        break;

    // 3. Get Categories
    case 'get_categories':
        $categories = readJsonFile($categoriesFile, []);
        $products = readJsonFile($productsFile, []);
        
        // Compute real-time counts
        $counts = [];
        foreach ($products as $p) {
            $c = $p['category'] ?? '';
            $counts[$c] = ($counts[$c] ?? 0) + 1;
        }

        foreach ($categories as &$cat) {
            $cat['count'] = $counts[$cat['id']] ?? 0;
        }
        unset($cat);

        jsonResponse([
            'success' => true,
            'categories' => $categories,
            'total_products' => count($products)
        ]);
        break;

    // 4. Admin Login
    case 'login':
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');

        if ($username === ADMIN_USER && $password === ADMIN_PASS) {
            $_SESSION['om_yaman_admin'] = true;
            $_SESSION['admin_user'] = ADMIN_USER;
            $_SESSION['login_time'] = time();
            jsonResponse([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'user' => [
                    'username' => ADMIN_USER,
                    'name' => 'أم يمان (المدير العام)'
                ]
            ]);
        } else {
            jsonResponse([
                'success' => false,
                'message' => 'اسم المستخدم أو كلمة المرور غير صحيحة'
            ], 401);
        }
        break;

    // 5. Admin Logout
    case 'logout':
        $_SESSION = [];
        if (session_id() !== '') {
            session_destroy();
        }
        jsonResponse(['success' => true, 'message' => 'تم تسجيل الخروج بنجاح']);
        break;

    // 6. Check Auth
    case 'check_auth':
        if (isAdminLoggedIn()) {
            jsonResponse([
                'success' => true,
                'authenticated' => true,
                'user' => [
                    'username' => ADMIN_USER,
                    'name' => 'أم يمان (المدير العام)'
                ]
            ]);
        } else {
            jsonResponse(['success' => true, 'authenticated' => false]);
        }
        break;

    // 7. Add Product (Admin Only)
    case 'add_product':
        if (!isAdminLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'غير مصرح بالدخول'], 403);
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $name = trim($input['name'] ?? '');
        $category = trim($input['category'] ?? 'blends');
        $price = floatval($input['price'] ?? 0);
        $weight = trim($input['weight'] ?? '250 جم');
        $description = trim($input['description'] ?? '');
        $image = trim($input['image'] ?? '');
        $badge = trim($input['badge'] ?? '');
        $inStock = isset($input['in_stock']) ? (bool)$input['in_stock'] : true;
        $featured = isset($input['featured']) ? (bool)$input['featured'] : false;

        if (empty($name) || $price <= 0) {
            jsonResponse(['success' => false, 'message' => 'الرجاء إدخال اسم المنتج وسعر صالح'], 400);
        }

        $products = readJsonFile($productsFile, []);
        $maxId = 0;
        foreach ($products as $p) {
            if ($p['id'] > $maxId) {
                $maxId = $p['id'];
            }
        }

        $newId = $maxId + 1;
        $newProduct = [
            'id' => $newId,
            'sku' => sprintf('OY-%03d', $newId),
            'name' => $name,
            'category' => $category,
            'price' => $price,
            'currency' => '₪',
            'weight' => $weight,
            'description' => $description,
            'image' => !empty($image) ? $image : 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=800&q=80',
            'badge' => $badge,
            'in_stock' => $inStock,
            'featured' => $featured
        ];

        // Prepend so it shows first
        array_unshift($products, $newProduct);
        writeJsonFile($productsFile, $products);

        jsonResponse([
            'success' => true,
            'message' => 'تمت إضافة المنتج بنجاح',
            'product' => $newProduct
        ]);
        break;

    // 8. Update Product (Admin Only)
    case 'update_product':
        if (!isAdminLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'غير مصرح بالدخول'], 403);
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int)($input['id'] ?? 0);

        if ($id <= 0) {
            jsonResponse(['success' => false, 'message' => 'معرف المنتج غير صحيح'], 400);
        }

        $products = readJsonFile($productsFile, []);
        $found = false;
        $updatedProduct = null;

        foreach ($products as &$p) {
            if ($p['id'] === $id) {
                if (isset($input['name'])) $p['name'] = trim($input['name']);
                if (isset($input['category'])) $p['category'] = trim($input['category']);
                if (isset($input['price'])) $p['price'] = floatval($input['price']);
                if (isset($input['weight'])) $p['weight'] = trim($input['weight']);
                if (isset($input['description'])) $p['description'] = trim($input['description']);
                if (isset($input['image'])) $p['image'] = trim($input['image']);
                if (isset($input['badge'])) $p['badge'] = trim($input['badge']);
                if (isset($input['in_stock'])) $p['in_stock'] = (bool)$input['in_stock'];
                if (isset($input['featured'])) $p['featured'] = (bool)$input['featured'];
                
                $found = true;
                $updatedProduct = $p;
                break;
            }
        }
        unset($p);

        if (!$found) {
            jsonResponse(['success' => false, 'message' => 'المنتج غير موجود'], 404);
        }

        writeJsonFile($productsFile, $products);
        jsonResponse([
            'success' => true,
            'message' => 'تم تحديث بيانات المنتج بنجاح',
            'product' => $updatedProduct
        ]);
        break;

    // 9. Delete Product (Admin Only)
    case 'delete_product':
        if (!isAdminLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'غير مصرح بالدخول'], 403);
        }

        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $id = (int)($input['id'] ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            jsonResponse(['success' => false, 'message' => 'معرف المنتج غير صحيح'], 400);
        }

        $products = readJsonFile($productsFile, []);
        $initialCount = count($products);
        $products = array_values(array_filter($products, function($p) use ($id) {
            return $p['id'] !== $id;
        }));

        if (count($products) === $initialCount) {
            jsonResponse(['success' => false, 'message' => 'المنتج غير موجود'], 404);
        }

        writeJsonFile($productsFile, $products);
        jsonResponse(['success' => true, 'message' => 'تم حذف المنتج بنجاح']);
        break;

    // 10. AI Chat Assistant ("Chat Om Yaman")
    case 'ai_chat':
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $message = trim($input['message'] ?? '');

        if (empty($message)) {
            jsonResponse(['success' => false, 'message' => 'الرسالة فارغة'], 400);
        }

        $reply = generateOmYamanResponse($message, $productsFile);
        jsonResponse([
            'success' => true,
            'reply' => $reply['text'],
            'suggested_products' => $reply['products'],
            'quick_suggestions' => $reply['suggestions']
        ]);
        break;

    default:
        jsonResponse([
            'success' => false,
            'message' => 'إجراء غير معروف',
            'status' => 'Om Yaman Spices API is running successfully'
        ], 400);
        break;
}

/**
 * Intelligent Om Yaman AI Expert Engine
 */
function generateOmYamanResponse($msg, $productsFile) {
    $msgLower = mb_strtolower($msg, 'UTF-8');
    $products = readJsonFile($productsFile, []);

    // Helper to find products by keywords
    $findProducts = function($keywords, $limit = 3) use ($products) {
        $matched = [];
        foreach ($products as $p) {
            $target = mb_strtolower($p['name'] . ' ' . $p['description'], 'UTF-8');
            foreach ($keywords as $kw) {
                if (strpos($target, mb_strtolower($kw, 'UTF-8')) !== false) {
                    $matched[] = $p;
                    break;
                }
            }
            if (count($matched) >= $limit) break;
        }
        return $matched;
    };

    // Recipe & Cooking Inquiries
    if (strpos($msgLower, 'منسف') !== false) {
        return [
            'text' => "يا هلا بيك يا غالي! أسرار المنسف البلدي على أصوله تبدأ ببهار المنسف الفاخر تبعنا مع الجميد الكركي، والهيل الأخضر الغواتيمالي حبة بحبة مع رشة السمنة الغنمية البلدية! لا تنسى تزينه بالصنوبر واللوز البلدي المحمر. شو رأيك أجهزلك البهارات بالسلة؟",
            'products' => $findProducts(['منسف', 'صنوبر', 'سمنة بلدية غنمية']),
            'suggestions' => ['أضف بهار المنسف للسلة', 'طريقة طبخ الجميد', 'أفضل سمنة بلدية']
        ];
    }

    if (strpos($msgLower, 'مقلوبة') !== false) {
        return [
            'text' => "يسعد قلبك! المقلوبة الفلسطينية الأصيلة سرها في خلطة المقلوبة تبعتنا (قرفة، بهار حلو، زنجبيل وهيل)، مع رشة كركم صافي بيعطي الرز لون ذهبي يشهي! تحب تجرب خلطتنا الخاصة للمقلوبة؟",
            'products' => $findProducts(['مقلوبة', 'كركم', 'لوز']),
            'suggestions' => ['خلطة المقلوبة الأصلية', 'أفضل بهارات دجاج', 'صنوبر ولوز']
        ];
    }

    if (strpos($msgLower, 'مسخن') !== false || strpos($msgLower, 'سماق') !== false) {
        return [
            'text' => "المسخن الفلاحي الفلسطيني تاجه السماق البلدي الصافي المعصور بدون ملح مع زيت الزيتون البكر الممتاز! إذا سماقك مش حامض وفاقع، المسخن ما بكون مسخن! عندنا سماق قطفة هالسنة بلدي 100%.",
            'products' => $findProducts(['سماق', 'مسخن', 'زيت زيتون']),
            'suggestions' => ['سماق بلدي فلاحي', 'زيت زيتون بكر ممتاز', 'بهارات دجاج مشوي']
        ];
    }

    if (strpos($msgLower, 'قدرة') !== false) {
        return [
            'text' => "القدرة الخليلية بالفرن الطيني بدها بهار القدرة الخليلية الأصلي مع حبات الحمص البلدي وراس ثوم كامل وسمنة بلدية! نكهة فخارية بتبيض الوجه قدام الضيوف.",
            'products' => $findProducts(['قدرة', 'حمص حب', 'سمنة']),
            'suggestions' => ['بهار قدرة خليلية', 'حمص بلدي حبة كبيرة', 'هيل حب فاخر']
        ];
    }

    if (strpos($msgLower, 'شاورما') !== false) {
        return [
            'text' => "تكرم عيونك! لشاورما دجاج أو لحمة أطيب من المحلات: ملعقة كبيرة من توابل الشاورما مع عصرة ليمون ولبن زبادي وثوم مهروس وزيت زيتون، انقعها ساعتين واشويها ع المقلاة الحامية وشوف الريحة!",
            'products' => $findProducts(['شاورما', 'ثوم', 'بابريكا']),
            'suggestions' => ['توابل شاورما لحم', 'توابل شاورما دجاج', 'بودرة ثوم بلدي']
        ];
    }

    if (strpos($msgLower, 'زعتر') !== false || strpos($msgLower, 'دقة') !== false || strpos($msgLower, 'فطور') !== false) {
        return [
            'text' => "فطورك الفلسطيني ما بيكمل إلا بزعتر أم يمان البلدي إكسترا مع السمسم المحمص وزيت الزيتون المعصور ع البارد، مع شوية دقة غزاوية بالعين جرادة الحارة! صحتين وهنا سلفاً.",
            'products' => $findProducts(['زعتر', 'دقة', 'زيت زيتون']),
            'suggestions' => ['زعتر فلسطيني بلدي إكسترا', 'دقة غزة الحمراء', 'زيت زيتون جليلي']
        ];
    }

    if (strpos($msgLower, 'شاي') !== false || strpos($msgLower, 'أعشاب') !== false || strpos($msgLower, 'ميرمية') !== false || strpos($msgLower, 'بابونج') !== false) {
        return [
            'text' => "يا سلام على ريحة الميرمية الجبلية والبابونج والزهورات الملكية! عندنا ميرمية قطف يدوي من جبال فلسطين بتفوح بالبيت كله، وزهورات مهدئة للأعصاب وتساعدك على نوم عميق ومريح.",
            'products' => $findProducts(['ميرمية', 'بابونج', 'زهورات']),
            'suggestions' => ['ميرمية برية فلسطينية', 'زهورات شامية مشكلة', 'كركديه أسواني']
        ];
    }

    if (strpos($msgLower, 'كبسة') !== false || strpos($msgLower, 'برياني') !== false) {
        return [
            'text' => "لكبسة ملكية تفوح ريحتها لآخر الحارة: استعمل بهار الكبسة الملكي مع حبة لومي أسود عماني وعود قرفة سيلاني وشوية هيل أخضر، ولا تنسى خيوط الزعفران لإعطاء الرز هيبة!",
            'products' => $findProducts(['كبسة', 'برياني', 'لومي', 'زعفران']),
            'suggestions' => ['بهار كبسة خليجية', 'زعفران إسباني', 'لومي عماني']
        ];
    }

    if (strpos($msgLower, 'توصيل') !== false || strpos($msgLower, 'شحن') !== false || strpos($msgLower, 'طلب') !== false || strpos($msgLower, 'سعر') !== false) {
        return [
            'text' => "أهلاً وسهلاً بك! التوصيل متوفر لجميع المناطق والمدن والقرى. يمكنك إضافة ما تحتاجه للسلة والضغط على 'إتمام الطلب عبر واتساب'، وسيصلك طلبك مغلفاً بعناية وطازجاً!",
            'products' => array_slice($products, 0, 3),
            'suggestions' => ['كيف أطلب عبر الواتساب؟', 'عرض الأكثر مبيعاً', 'أصناف الزعتر والزيوت']
        ];
    }

    // Default warm motherly response
    return [
        'text' => "يا مية أهلاً وسهلاً بيك في متجر أم يمان للبهارات والتوابل البلدية! أنا هون لمساعدتك في اختيار أنسب البهارات لطبختك أو نصائح الوصفات التراثية الفلسطينية والشرقية. بدك بهارات لأكلة معينة متل المنسف أو المسخن أو خلطات الأعشاب؟",
        'products' => array_slice($products, 0, 3),
        'suggestions' => ['بهارات المنسف الأردني', 'سر خلطة المسخن والسماق', 'زعتر بلدي ودقة غزة', 'أعشاب وزهورات طبيعية']
    ];
}
?>
