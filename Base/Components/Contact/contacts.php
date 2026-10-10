<?php
$contacts = [
    [
        'icon' => 'phone',
        'name' => 'Phone',
        'title' => 'Feel free to call us anytime during working hours.',
        'info' => '+355 68-123-4567',
        'link' => 'tel:+355681234567',
        'type' => '',
        'style' => '',
    ],
    [
        'icon' => 'mail',
        'name' => 'Email',
        'title' => 'We usually respond to emails within 24 hours.',
        'info' => 'user@gmail.com',
        'link' => 'mailto:contact@example.com',
        'type' => '',
        'style' => '',
    ],
    [
        'icon' => 'map-pin',
        'name' => 'Location',
        'title' => 'Visit our office at the address below:',
        'info' => 'View on Google Maps',
        'link' => 'https://www.google.com/maps?q=Komuna+e+Parisit,+Tirana,+Albania',
        'type' => 'iframe',
        'style' => 'video-play-icon read-more lightbox',
    ]
];
?>

<?php foreach ($contacts as $item): ?>
    <div class="text-center px-6">
        <div class="relative text-transparent">
            <div class="size-20 bg-red-500/5 text-red-500 rounded-xl text-2xl flex align-middle justify-center items-center mx-auto shadow-xs dark:shadow-gray-800">
                <i data-feather="<?php echo $item['icon']; ?>"></i>
            </div>
        </div>

        <div class="content mt-7">
            <h5 class="h5 text-lg font-semibold"><?php echo $item['name']; ?></h5>
            <p class="text-slate-400 mt-3"><?php echo $item['title']; ?></p>
            
            <div class="mt-5">
                <a href="<?php echo $item['link']; ?>"
                data-type="<?php echo $item['type']; ?>" class="<?php echo $item['style']; ?> text-red-500 font-medium"><?php echo $item['info']; ?></a>
            </div>
        </div>
    </div>
<?php endforeach; ?>