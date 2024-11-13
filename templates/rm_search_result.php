<?php
$rm_data = get_transient('rm_response');

foreach ($rm_data['results'] as $result){?>
    <div class="rm_card">
        <div class="rm_char_img">
            <img src="<?php echo $result['image'];?>" alt="<?php echo $result['name'];?>">
        </div>
        <span>Name: <?php echo $result['name'];?></span>
        <span>Status: <?php echo $result['status'];?></span>
        <span>Species: <?php echo $result['species'];?></span>
        <?php if($result['type']){ ?><span>Type: <?php echo $result['type'];?></span> <?php } ?>
        <span>Gender: <?php echo $result['gender'];?></span>
    </div>
<?
}
?>

