<?php
/*

PHP Image Host
www.phpace.com/php-image-host

Copyright (c) 2004,2008 Sam Yapp

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

class upgradeAction extends Action
{
    function run()
    {
        // get current db version
        require_once INCLUDE_DIR . DIRECTORY_SEPARATOR . 'dbschema.inc.php';
        $current_version = $this->app->db->getSchemaVersion();
        $new_version = $this->app->db->getSchemaVersion($dbschema);
        $upgraded = false;
        $errors = '';
        if( isset($_POST['upgrade']) ) {
            $upgraded = $this->app->db->setupSchema($dbschema);
            if( !$upgraded ) {
                $errors = 'An error occurred whilst upgrading:<br /><br />' . $this->app->db->lastError;
            }
        }
        $sql = array();
        $sqls = $this->app->db->getUpgradeSQL($dbschema);
        foreach( $sqls as $comment => $statements) {
            $sql[] = "\n\n".'/** '.$comment.' **/';
            if( is_array($statements) ) {
                foreach( $statements as $stmt ) {
                    $sql[] = $stmt.';';
                }
            }
            else {
                $sql[] = $statements.';';
            }
        }
        $sql = join("\n\n", $sql);
        $this->theme->assign('upgrade_sql', $sql);
        $this->theme->assign('errors', $errors);
        $this->theme->assign('current_version', $current_version);
        $this->theme->assign('new_version', $new_version);
        $this->theme->assign('upgraded', $upgraded);
    }
}
