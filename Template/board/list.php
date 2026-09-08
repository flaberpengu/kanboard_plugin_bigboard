<section id="main">
    <div class='page-header'>
        <h2><?php echo t('BigBoard'); ?> : <?php echo t('manage project selection'); ?></h2>
    </div>
	<form id="project-creation-form" method="post" action="<?php echo $this->url->href('Bigboard', 'saveList', ['plugin' => 'bigboard']); ?>" autocomplete="off">
	<div>
		<small>
			<span class="btn" id="selectAll"><?php echo t('check'); ?> <b><?php echo t('everything'); ?></b></span>
			<span class="btn" id="clearAll"><?php echo t('uncheck'); ?> <b><?php echo t('everything'); ?></b></span>
			<?php
                // try in case plugin starred projects is not available
                try {
                    if (method_exists($this->app->starredProjectsModel, 'find')) { ?>
			<span class="btn" id="addStar"><?php echo t('add'); ?> <b><?php echo t('favorites'); ?></b></span>
			<span class="btn" id="onlyStar"><?php echo t('check'); ?> <u><?php echo t('only'); ?></u> <b><?php echo t('favorites'); ?></b></span>
			<?php   }
                } catch (Exception $e) {
                }
            ?> 
			<hr>
		</small>
	</div>
<?php
if (isset($_GET['boardview'])) {
                echo "<input type=hidden name='boardview' value=1>";
            }

$storedList = $this->app->bigboardModel->selectFindAllProjectsById($this->user->getId());
(count($storedList) > 0) ? sort($storedList) : null;
?>
<div id="bigboard-project-list">
<?php
foreach ($projectList as $project) {
    if ((null != $storedList) && (in_array($project['id'], $storedList))) {
        $stored = 'checked';
    } else {
        $stored = '';
    }

    try {
        if ($this->app->starredProjectsModel->find($project['id'], $this->user->getId())) {
            $fav = "<i class='fa fa-star' title='".t('Favorite project')."'></i>";
            $class = 'fav';
        } else {
            $fav = $class = '';
        }
    } catch (Exception $e) {
        $fav = $class = '';
    }

    if ($project['is_private']) {
        $priv = "<i class='fa fa-lock fa-fw' title='".t('Private project')."'></i>";
    } else {
        $priv = '';
    } ?>
<div class="selitem" data-project-id="<?php echo $project['id']; ?>" style="display: block;">
<span class="drag-handle"><i class="fa fa-arrows-alt" title="<?php echo t('Change position'); ?>"></i></span>
<label class="sel">
<input type="checkbox" name="selection[]" class="<?php echo $class; ?>" value="<?php echo $project['id']; ?>" <?php echo $stored; ?>>
  <span> <small>#<?php echo $this->text->e($project['id']); ?></small> <?php echo $fav; ?> <?php echo $project['nom']; ?> <?php echo $priv; ?> </span> 
</label>
</div>			

<?php
} ?>
</div>
        <?php echo $this->modal->submitButtons(); ?>
    </form>
</section>	 