<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

/*
 * Home page ("Modern Luxury" design, styles in css/theme-luxury.css).
 * Featured products come from the database; the other sections are fixed
 * content for now.
 *
 * PLACEHOLDERS to replace before launch:
 *  - photos in images/home/ (sample stock photos; use the business's own),
 *  - the figures in the "Our story" section,
 *  - the customer reviews (use real ones, with permission).
 * Category tiles link to the shop until categories are set up (catalogue phase).
 */

$featured = (new ProductController())->getFeaturedProducts(4);

$shopUrl = BASE_URL . 'views/shop.php';
$contactUrl = BASE_URL . 'views/pages/contact.php';
$img = BASE_URL . 'images/home/';

$rooms = [
    ['Living Room', 'Sofas, chairs & tables', 'living-warm.jpg', 'object-position: 62% 50%'],
    ['Bedroom', 'Beds & wardrobes', 'bedroom-luxe.jpg', ''],
    ['Dining', 'Tables & chairs', 'dining-green.jpg', ''],
    ['Kitchen', 'Fitted cabinetry', 'kitchen-wood.jpg', ''],
    ['Office', 'Desks & storage', 'office-green.jpg', ''],
];

$decor = [
    ['Rugs & Carpets', 'decor-rug.jpg'],
    ['Mirrors', 'decor-mirror.jpg'],
    ['Lighting', 'decor-pendants.jpg'],
    ['Wall Art', 'decor-wallart.jpg'],
    ['Vases & Objects', 'decor-vases.jpg'],
];

$reviews = [
    ['Our dining table is the heart of our home now. The finish is simply beautiful.', 'Ama O.', 'Kumasi'],
    ['They built a wardrobe to fit an awkward corner perfectly. Worth every cedi.', 'Kwame A.', 'Obuasi'],
    ['From quotation to installation, the whole experience felt personal and premium.', 'Efua M.', 'Accra'],
];

$pageDescription = CC_SITE_NAME . ': handmade furniture from our Obuasi workshop, bespoke pieces made to measure, and home décor from trusted suppliers.';
require_once __DIR__ . '/layout/header.php';
?>

<main class="lx-home" id="main-content">

    <section class="lx-hero">
        <img src="<?php echo $img; ?>hero-living.jpg" alt="A warm living room with a sofa, leather ottomans and a wooden coffee table">
        <div class="lx-shell">
            <span class="lx-label" style="color: var(--lx-gold-soft)">Handmade in Obuasi</span>
            <h1>Furniture crafted to be <em>lived with</em>, for generations</h1>
            <p>Made in our own workshop from carefully selected hardwoods. Finished by hand and delivered to your home.</p>
            <div class="lx-hero-actions">
                <a class="lx-btn lx-btn--gold" href="<?php echo $shopUrl; ?>">Explore the collection</a>
                <a class="lx-btn lx-btn--ghost" href="#bespoke">Commission a piece</a>
            </div>
        </div>
        <div class="lx-hero-badge">
            <span class="lx-label">Bespoke service</span>
            <p>Your design, your dimensions, your finish.</p>
        </div>
    </section>

    <div class="lx-strip">
        <div class="lx-shell">
            <div><strong>Handmade</strong><span>in our own workshop</span></div>
            <div><strong>Made to measure</strong><span>for any room</span></div>
            <div><strong>Solid hardwoods</strong><span>built to last</span></div>
            <div><strong>Delivered</strong><span>and assembled for you</span></div>
        </div>
    </div>

    <section class="lx-section" id="collections">
        <div class="lx-shell">
            <div class="lx-head">
                <div>
                    <span class="lx-label">Shop by room</span>
                    <h2>Collections for <em>every</em> room</h2>
                </div>
                <a class="lx-link-line" href="<?php echo $shopUrl; ?>">View all furniture</a>
            </div>
            <div class="lx-cats">
                <?php foreach ($rooms as [$name, $sub, $file, $style]): ?>
                    <a class="lx-cat" href="<?php echo $shopUrl; ?>">
                        <img src="<?php echo $img . $file; ?>" alt="<?php echo e($name); ?> furniture"<?php echo $style ? ' style="' . e($style) . '"' : ''; ?> loading="lazy">
                        <div class="lx-cat-copy"><span><?php echo e($sub); ?></span><h3><?php echo e($name); ?></h3></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="lx-section lx-featured">
        <div class="lx-shell">
            <div class="lx-head">
                <div>
                    <span class="lx-label">New in</span>
                    <h2>Featured <em>pieces</em></h2>
                </div>
                <a class="lx-link-line" href="<?php echo $shopUrl; ?>">Shop all</a>
            </div>

            <?php if ($featured): ?>
                <div class="lx-products">
                    <?php foreach ($featured as $product): ?>
                        <?php
                        $productUrl = BASE_URL . 'views/single_product.php?pro_id=' . (int) $product['product_id'];
                        // Only use the saved image if the file is actually on the server.
                        $image = basename(trim($product['product_image'] ?? ''));
                        $hasImage = $image !== '' && is_file(__DIR__ . '/../images/products/' . $image);
                        ?>
                        <article class="lx-product">
                            <a class="lx-pic" href="<?php echo $productUrl; ?>">
                                <?php if ($hasImage): ?>
                                    <img src="<?php echo BASE_URL; ?>images/products/<?php echo rawurlencode($image); ?>" alt="<?php echo e($product['product_title']); ?>" loading="lazy">
                                <?php else: ?>
                                    <span class="lx-nopic">Photo coming soon</span>
                                <?php endif; ?>
                            </a>
                            <h3><a href="<?php echo $productUrl; ?>"><?php echo e($product['product_title']); ?></a></h3>
                            <div class="lx-price"><?php echo e(cc_money($product['product_price'])); ?></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="lx-empty">
                    <h3>Our collection is on its way</h3>
                    <p>New pieces are being photographed in the workshop. In the meantime, ask us about a custom piece.</p>
                    <a class="lx-btn lx-btn--gold" href="#bespoke">Commission a piece</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="lx-bespoke" id="bespoke">
        <div class="lx-bespoke-grid">
            <div class="lx-bespoke-pics">
                <img class="lx-a" src="<?php echo $img; ?>kitchen-wood.jpg" alt="Fitted wooden kitchen cabinets" loading="lazy">
                <img class="lx-b" src="<?php echo $img; ?>hero-carving.jpg" alt="A craftsman shaping wood by hand" style="object-position: 30% 70%" loading="lazy">
            </div>
            <div class="lx-bespoke-copy">
                <span class="lx-label">Bespoke furniture</span>
                <h2>Designed by you, <em>made by us</em></h2>
                <p>From a fitted wardrobe to a full kitchen, our craftsmen build to your design, dimensions and finish. Share your idea and we will send you a quotation before any work begins.</p>
                <ol class="lx-steps">
                    <li><b>01</b><span>Share your idea<small>Send sketches, photos and measurements</small></span></li>
                    <li><b>02</b><span>Receive a quotation<small>We confirm materials, finish and price</small></span></li>
                    <li><b>03</b><span>Crafted in Obuasi<small>Built by hand in our workshop</small></span></li>
                    <li><b>04</b><span>Delivered &amp; installed<small>Placed carefully in your home</small></span></li>
                </ol>
                <?php /* Points at the contact form until the custom request page is built. */ ?>
                <a class="lx-btn lx-btn--ghost" href="<?php echo $contactUrl; ?>">Request a custom piece</a>
            </div>
        </div>
    </section>

    <section class="lx-section" id="decor">
        <div class="lx-shell">
            <div class="lx-decor-intro">
                <div>
                    <span class="lx-label">Home décor</span>
                    <h2 style="margin-top: 18px">The finishing <em>touches</em></h2>
                </div>
                <div>
                    <p>A curated edit of rugs, curtains, mirrors, lighting and accessories, chosen to go with our furniture.</p>
                    <span class="lx-source">Sourced from trusted suppliers</span>
                </div>
            </div>
            <div class="lx-decor-row">
                <?php foreach ($decor as [$name, $file]): ?>
                    <a class="lx-decor-item" href="<?php echo $shopUrl; ?>">
                        <span class="lx-pic"><img src="<?php echo $img . $file; ?>" alt="<?php echo e($name); ?>" loading="lazy"></span>
                        <h3><?php echo e($name); ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="lx-section lx-story" id="story">
        <div class="lx-shell lx-story-grid">
            <div class="lx-frame"><img src="<?php echo $img; ?>craft-saw.jpg" alt="A craftsman cutting timber in the workshop" loading="lazy"></div>
            <div>
                <span class="lx-label">Our story</span>
                <h2>A workshop built on <em>patience</em> and precision</h2>
                <p>Every piece begins as raw timber in our Obuasi workshop. Our craftsmen measure, cut, join and finish each item by hand.</p>
                <p>Now we are bringing that same care online, so you can furnish your home from anywhere in Ghana.</p>
                <?php /* PLACEHOLDER figures: confirm with the business. */ ?>
                <div class="lx-stats">
                    <div><strong>15+</strong><span>Years of craft</span></div>
                    <div><strong>2,000</strong><span>Pieces made</span></div>
                    <div><strong>100%</strong><span>Made in Ghana</span></div>
                </div>
                <p style="margin-top: 36px"><a class="lx-link-line" href="<?php echo BASE_URL; ?>views/pages/about.php">Read our story</a></p>
            </div>
        </div>
    </section>

    <?php /* PLACEHOLDER reviews: replace with real customer reviews before launch. */ ?>
    <section class="lx-section lx-quotes">
        <div class="lx-shell">
            <span class="lx-label">Kind words</span>
            <div class="lx-quote-grid">
                <?php foreach ($reviews as [$text, $name, $town]): ?>
                    <div class="lx-quote">
                        <div class="lx-stars" aria-hidden="true">★★★★★</div>
                        <blockquote>“<?php echo e($text); ?>”</blockquote>
                        <cite><?php echo e($name); ?> · <?php echo e($town); ?></cite>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
