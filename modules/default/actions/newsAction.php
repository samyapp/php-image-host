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

class newsAction extends action
{
    public function run()
    {
        $news = $this->app->loadClass('news');
        $news_item = $news->getNewsById($this->app->getParamInt('article'));
        $news_items = null;
        if( !$news_item ) {
            if( $this->app->getParamStr('status') == 'archived' ) {
                $news_items = $news->getArchivedNews();
            }
            else {
                $news_items = $news->getCurrentNews();
            }
        }
        if( !is_array($news_items) ) {
            $news_items = array();
        }
        $this->theme->assign('news_item', $news_item);
        $this->theme->assign('news_items', $news_items);
        $this->theme->assign('archived_news', $this->app->getParamStr('status') == 'archived');
    }
}