<?php

use Framework\Session;

?>

<?php $successMessage = Session::get('flash_success_message'); ?>
<?php if ($successMessage !== null) : ?>
    <div class="message bg-green-100 p-3 my-3">
        <?= $successMessage ?>
    </div>
<?php endif; ?>


<?php $errorMessage = Session::get('flash_error_message'); ?>
<?php if ($errorMessage !== null) : ?>
    <div class="message bg-red-100 p-3 my-3">
        <?= $errorMessage ?>
    </div>
<?php endif; ?>