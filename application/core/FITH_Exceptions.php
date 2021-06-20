<?php
class FITH_Exceptions extends CI_Exceptions
{
    function show_404($page = '', $log_error = TRUE)
    {
        parent::show_404($page, FALSE);
    }
}