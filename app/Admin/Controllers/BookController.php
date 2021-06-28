<?php

namespace App\Admin\Controllers;

use App\Models\Book;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class BookController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Book';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Book());
        $grid->filter(function($filter){
            // Add a column filter
            $filter->equal('status')->select(['0' => '未借阅','1'=> '已借阅']);
            $filter->where(function($query){
                $query->where('ISBN','like',"%{$this->input}%");
            },'ISBN');

        });
        $grid->disableExport();
        $grid->model()->orderBy('created_at', 'desc');
        $grid->column('id', __('Id'));
        $grid->column('name', __('书名'));
        $grid->column('ISBN', __('ISBN'));
        $grid->column('author', __('作者'));
        $grid->column('publish', __('出版社'));
        $grid->column('pubdate', __('出版日期'));
        $grid->column('status', __('借阅状态'))->display(function ($value) {
            return $value ? '已借阅' : '未借阅';
        });;
        $grid->actions(function ($actions) {
            $actions->disableView();
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Book::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('ISBN', __('ISBN'));
        $show->field('author', __('Author'));
        $show->field('publish', __('Publish'));
        $show->field('pubdate', __('Pubdate'));
        $show->field('status', __('Status'));
        $show->field('local', __('Local'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Book());

        $form->text('name', __('书名'))->rules('required');
        $form->text('ISBN', __('ISBN'))->rules('required|regex:/\d{13}/', [
            'regex' => 'ISBN号必须为13位整数',
        ]);
        $form->text('author', __('作者'))->rules('required');
        $form->text('publish', __('出版社'))->rules('required');
        $form->datetime('pubdate', __('出版日期'))->rules('required|date');
        //$form->radio('status', __('Status'))->options([0 => '未借阅', 1 => '已借阅'])->default(0);
        $form->text('local', __('存放位置'))->rules('required');

        return $form;
    }

    public function create(Content $content)
    {
        return $content->header('图书管理')->title('新增图书')->body($this->form());
    }

    public function index(Content $content)
    {
        return $content->header('图书列表')->body($this->grid());
    }
}
