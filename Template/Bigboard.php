<li>
	<i class="fa fa-th-large fa-fw"></i> 
    <?= $this->url->link(
        t('BigBoard'),
        'Bigboard',
        'index',
        ['plugin' => 'Bigboard', 'search' => 'status:open', ]
    ) ?>
</li>
