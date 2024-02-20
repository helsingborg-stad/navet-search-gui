<?php $__env->startSection('article'); ?>
        <?php $__env->startComponent('Typography.typography', [
        'element' => 'h1',
        'classList' => ['u-color__text--primary', 'u-margin__bottom--2']
    ]); ob_start(); ?>
            <?php $__env->startComponent('Icon.icon', ['icon' => 'person_search', 'size' => 'inherit']); ob_start(); ?>
            <?php echo ob_get_clean(); echo $__env->renderComponent(); ?>
        Sök person
        <?php echo ob_get_clean(); echo $__env->renderComponent(); ?>
    <?php echo $__env->renderWhen(!isset($searchResult), 'partials.sok.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
    <?php echo $__env->renderWhen(isset($searchResult) && $searchResult, 'partials.sok.result', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.containers.page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/seno1000/www/public/navet/frontend/app/views/pages//sok.blade.php ENDPATH**/ ?>