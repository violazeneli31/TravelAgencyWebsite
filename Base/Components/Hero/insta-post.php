<?php
$posts = [
    [
        'img' => '/images/listing/1.jpg', 
    ],
    [
        'img' => '/images/listing/2.jpg', 
    ],
    [
        'img' => '/images/listing/3.jpg', 
    ],
    [
        'img' => '/images/listing/4.jpg', 
    ],
    [
        'img' => '/images/listing/5.jpg', 
    ],
    [
        'img' => '/images/listing/6.jpg', 
    ],
    [
        'img' => '/images/listing/7.jpg', 
    ],
    [
        'img' => '/images/listing/8.jpg', 
    ],
    [
        'img' => '/images/listing/9.jpg', 
    ],
    [
        'img' => '/images/listing/10.jpg', 
    ],
    [
        'img' => '/images/listing/11.jpg', 
    ],
    [
        'img' => '/images/listing/12.jpg', 
    ],
    [
        'img' => '/images/listing/5.jpg', 
    ],
    [
        'img' => '/images/listing/8.jpg', 
    ],
    [
        'img' => '/images/listing/4.jpg', 
    ]
];
?>

<?php foreach ($posts as $item): ?>
    <div class="tiny-slide">
        <a href="<?php echo $static_url, $item['img']; ?>" class="lightbox d-inline-block" title="">
            <img src="<?php echo $static_url, $item['img']; ?>" class="sm:size-40 object-cover" alt="">
        </a>
    </div>
<?php endforeach; ?>