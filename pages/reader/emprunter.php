<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('user');
redirect('/primelib/pages/reader/catalogue.php');
