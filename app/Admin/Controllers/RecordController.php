<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookReturnRequest;
use App\Http\Requests\Admin\RecordRequest;
use App\Models\Book;
use App\Models\Record;
use DateTime;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecordController extends Controller
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Record';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Record());
        $grid->filter(function($filter){
            // Add a column filter
            $filter->equal('status')->select(['0' => '未归还','1'=> '已归还']);
        });
        
        $grid->model()->orderBy('borrow_date', 'desc');
        $grid->column('id', __('Id'));
        $grid->column('user_id', __('User id'));
        $grid->column('user.name','用户名');
        $grid->column('book_id', __('Book id'));
        $grid->column('book.name','图书名');
        $grid->column('borrow_date', __('Borrow date'));
        $grid->column('return_date', __('Return date'));
        $grid->column('return_deadline', __('Return deadline'));
        $grid->column('status','状态')->display(function($value){
            return $value?'已归还':'未归还';
        });
        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->disableExport();
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
        $show = new Show(Record::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('book_id', __('Book id'));
        $show->field('borrow_date', __('Borrow date'));
        $show->field('return_date', __('Return date'));
        $show->field('return_deadline', __('Return deadline'));
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Record());

        $form->text('user_id', __('User id'));
        $form->text('book_id', __('Book id'));
        $form->datetime('borrow_date', __('借阅时间'))->default(date('Y-m-d H:i:s'));
        $form->datetime('return_deadline', __('最晚归还时间'))->default(date('Y-m-d H:i:s',strtotime("+2 months")));
        return $form;
    }

    public function show(Record $record)
    {
        return Admin::content(function (Content $content) use ($record) {
            $content->header('借阅记录');
            // body 方法可以接受 Laravel 的视图作为参数
            $content->body(view('admin.record.show', ['record' => $record]));
        });
    }

    public function index(Content $content){
        return $content->header('借阅列表')->body($this->grid());        
    }

    public function create(Content $content){
        return $content->title('新增记录')->header('图书借阅')->body($this->form());
    }

    public function store(RecordRequest $request){
        $records = $request->only('user_id','book_id','borrow_date','return_deadline');
        DB::transaction(function () use($records) {
            $record = new Record();
            $record->create($records);
            Book::find($records['book_id'])->update([
                'status' => '1'
            ]);
        });
        return redirect()->route('admin.records.index');
    }

    public function bookReturn(BookReturnRequest $request){
        $records = $request->only('return_date','id');
        $borrowDate = Record::find($records['id'])->get()[0];
        $borrowDate = $borrowDate->borrow_date.'';
        $borrowDate = strtotime($borrowDate);
        $return_date = strtotime($records['return_date']);
        if($borrowDate > $return_date){
            return response()->json([
                'message' => '归还时间不得早于借阅时间'
            ],400);
        }
        DB::transaction(function() use ($records){
            $record = Record::find($records['id']);
            $record->update([
                'status' => '1',
                'return_date' => $records['return_date']
            ]);
            Book::find($record->book->id)->update([
                'status' => '0'
            ]);
        });

        return;
       
    }
}
