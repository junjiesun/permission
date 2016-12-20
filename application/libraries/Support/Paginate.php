<?php

namespace Lib\Support;


class Paginate
{
    private $everypage;           //一页显示的多少个分页块
    private $nowpage;             //当前页
    private $allpage;           //总页数
    private $sindex;              //起头页数
    private $eindex;             //结尾页数
    private $linkurl;            //获取当前的url
    /*
    * $show_pages
    * 使用方法：
    *  $pagesService=new PagesService('list?page={page}','1',$totalpage,3);
    *  $displayView= $pagesService->showPages();
    *  $parameters['view']=$displayView;
    * //===========================================
    *  传入链接，当前页，总页数，按钮个数
    *  页面中取出view，即为分页跳转按钮
    */
    private $show_pages;

    public function __construct($linkurl='', $nowpage=1, $allpage=1, $everypage=10)
    {
//        var_dump($everypage);
        $this->nowpage   = $this->numeric($nowpage);
        $this->allpage   = $this->numeric($allpage);
        $this->everypage = $this->numeric($everypage);
        $this->linkurl   = $linkurl;

        //$startend        = $this->getStartEnd( $this->everypage,$this->nowpage,$this->allpage);

        //$this->sindex    = $startend[0];
        //$this->eindex    = $startend[1];
    }

    private function getStartEnd($everypage,$nowpage,$allpage)
    {
        $midpage = $everypage/2;
        $sindex  = 1;

        if($allpage>$everypage)
        {
            if(($nowpage-$midpage)>0 && ($nowpage+$midpage < $allpage))
            {
               $sindex = intval($nowpage-$midpage) > 0 ? intval($nowpage-$midpage) : 1;
               $eindex = intval($nowpage+$midpage-1) > $allpage ? intval($nowpage+$midpage-1) : $allpage;
            }
            else if(intval($nowpage-$midpage)>0 && ($nowpage+$midpage >=$allpage ))
            {
               $sindex = $allpage-$everypage+1;
               $eindex = $allpage;
            }
        }else
        {
           $eindex = $allpage;
        }
        return array($sindex,$eindex);
    }

    //检测是否为数字
    private function numeric($num) 
    {
        if (strlen($num)) 
        {
            if (!preg_match("/^[0-9]+$/", $num)) {
                $num = 1;
            } else {
                $num = substr($num, 0, 11);
            }
        } else {
            $num = 1;
        }
        return $num;
    }

    //地址替换
    private function page_replace($page) 
    {
        return str_replace("{page}", $page, $this->linkurl);
    }

    //首页
    private function firstPage() 
    {
        return "<li ><a href='" . (1 != $this->nowpage ? $this->page_replace(1) : 'javascript:;;') . "' title='首页'><span aria-hidden='true'>首页</span></a></li>";
    }

    //尾页
    private function endPage() 
    {
        return "<li ><a href='" . ($this->allpage != $this->nowpage ? $this->page_replace($this->allpage) : 'javascript:;;'). "' aria-label='Last'  title='尾页'><span aria-hidden='true'>尾页</span></a></li>";
    }
    //上一页
    private function pageLast() 
    {
        $page = $this->nowpage == 1 ? 1 : ($this->nowpage-1);
        return "<li ><a href='" . ($page != $this->nowpage ? $this->page_replace($page) : 'javascript:;;') . "' aria-label='Previous'  title='上一页'><span aria-hidden='true'>&laquo;</span></a></li>";

    }
    //下一页
    private function pageNext()
    {
        $page = $this->nowpage==$this->allpage ? $this->allpage : ($this->nowpage+1) ;
        return "<li ><a href='" . ($page != $this->nowpage ? $this->page_replace($page) : 'javascript:;;') . "' aria-label='Next'  title='下一页'><span aria-hidden='true'>&raquo;</span></a></li>";
    }

    /**
     * [getMidPage 获取中间页页码]
     * @return [type] [description]
     */
    private function getMidPage()
    {
       
        $midpage = $this->everypage%2 == 0 ? $this->everypage/2 : ceil($this->everypage/2);
        return $midpage;
    }
    //输出
    public function showPages() 
    {
        if($this->allpage==1) return '';
        $midpage = $this->getMidPage();

        $str = '<nav style="text-align:center"><ul class="pagination" >';
        $str .= $this->firstPage();  //首页按钮
        $str .= $this->pageLast();  //上一页按钮
        $str .= $this->prePageBtn($midpage);  //当前页面前面页码按钮
        $str .= $this->nextPageBtn($midpage); //当前页面后面页码按钮
        $str .= $this->pageNext();  //下一页按钮
        $str .= $this->endPage(); //尾页按钮
        $str .= "</ul></nav>";
        return $str;
    }

    private function prePageBtn($midpage = 1)
    {
        $preBtnstr = '';
       
        $startIndex = $this->nowpage - $midpage > 0? $this->nowpage - $midpage : 1;
        $startIndex = $this->allpage-$this->nowpage > $midpage || $startIndex == 1 ? $startIndex : $this->nowpage-($this->everypage-($this->allpage-$this->nowpage));
        $endIndex = $this->nowpage;

        for ($startIndex; $startIndex < $endIndex; $startIndex++)
        {
            $css = '';
            if ($startIndex == $this->nowpage)
            {
                $css = 'style="background-color:#00acd6;color:#FFFFFF;"' ;
            }
            $preBtnstr .= '<li><a '.$css.' href="'.($startIndex != $this->nowpage ? $this->page_replace($startIndex) : 'javascript:;;').'" > '.$startIndex.'</a></li>';
        }

        return $preBtnstr;
    }

    private function nextPageBtn($midpage = 1)
    {
        $nextBtnstr = '';
        $startIndex = $this->nowpage;
        $endIndex = $this->everypage - $this->nowpage > $midpage ? $this->everypage : $startIndex+$midpage;
        $endIndex = $this->allpage-$this->nowpage < $midpage || $this->nowpage == $this->allpage ? $this->allpage : $endIndex;
      
        for ($startIndex; $startIndex <= $endIndex; $startIndex++)
        {
            $css = '';
            if ($startIndex == $this->nowpage)
            {
                $css = 'style="background-color:#00acd6;color:#FFFFFF;"' ;
            }
            $nextBtnstr .= '<li><a '.$css.' href="'.($startIndex != $this->nowpage ? $this->page_replace($startIndex) : 'javascript:;;').'" > '.$startIndex.'</a></li>';
        }
        return $nextBtnstr;
    }
} 