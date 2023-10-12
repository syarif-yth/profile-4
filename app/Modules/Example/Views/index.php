<div class="container">
    <div class="row">
        <div class="col col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            Module: <strong>Example</strong>
            <hr />
            <ul>
                <?php foreach ($examples ?? [] as $key => $example) : ?>
                    <li><?= $example->id ?>: <?= $example->title ?? '{title}' ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>