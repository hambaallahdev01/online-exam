<pre>
<?php
$output_including_status = shell_exec("git pull 2>&1; echo $?");
echo $output_including_status;
?>
</pre>