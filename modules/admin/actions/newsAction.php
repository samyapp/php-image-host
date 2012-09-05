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

class newsAction extends Action
{
    function run()
    {
        $news = $this->app->loadClass('news');
        $message = '';

        $news_id = $this->app->getParamInt('news_id');

        if( $this->app->getParamStr('create') ) {
            $headline = $this->app->getParamStr('headline');
            $news_id = $news->addNews($headline);
            if( $news_id ) {
                $message = 'News item created';
            }
        }
        $news_item = null;
        if( $news_id != 0 ){
            $news_item = $news->getNewsById($news_id);
        }

        if( $news_item ) {
            if( $this->app->getParamStr('update') ) {
                if( $news->updateNews($news_item->news_id, array(
                            'headline' => $this->app->getParamStr('headline'),
                            'summary' => $this->app->getParamStr('summary'),
                            'details' => $this->app->getParamStr('details'),
                            'status' => $this->app->getParamStr('status'),
                            'published' => $this->app->getParamStr('published')
                        ))) {
                    $news_item = $news->getNewsById($news_item->news_id);
                    $message = 'News item updated.';
                }
            }
            elseif( $this->app->getParamStr('delete') ) {
                $news->deleteNews($news_item->news_id);
                $news_id = $news_item = null;
                $message = 'News item deleted';
            }
            elseif( $this->app->getParamStr('changestatus') ) {
                if( $news->updateNews($news_item->news_id, array('status' => $this->app->getParamStr('status')) ) ) {
                    $message = 'News item updated';
                }
            }
        }
        $nstatus = 'unpublished';
        $items = null;
        if( $news_item ) {
            $this->theme->templateName = 'edit_news';
        }
        elseif( $this->app->getParamInt('add') ) {
            $this->theme->templateName = 'add_news';
        }
        else {
            $options = array();
            $nstatus = $this->app->getParamStr('nstatus');
            if( $nstatus ) {
                $options['status'] = $nstatus;
            }
            $items = $news->getNews($options);
        }
        foreach( array('message', 'news_item', 'nstatus', 'items') as $v ) {
            $this->theme->assign($v, $$v);
        }
    }
}
