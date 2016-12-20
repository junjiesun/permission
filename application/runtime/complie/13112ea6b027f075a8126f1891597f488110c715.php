<?php $__env->startSection('body'); ?>
    @parent
    <div class="col-sm-12">
        <blockquote class="text-warning" style="font-size:14px">
            欢迎您登入权限管理Demo

        </blockquote>

        <hr>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('clayout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>