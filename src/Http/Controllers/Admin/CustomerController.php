<?php

namespace Sudo\Order\Http\Controllers\Admin;
use Sudo\Base\Http\Controllers\AdminController;

use Illuminate\Http\Request;
use ListData;
use Form;
use ListCategory;

class CustomerController extends AdminController
{
    function __construct() {
        $this->models = new \Sudo\Order\Models\Customer;
        $this->table_name = $this->models->getTable();
        $this->module_name = 'Khách hàng';
        $this->has_seo = false;
        $this->has_locale = false;
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $requests) {
        $listdata = new ListData($requests, $this->models, 'Order::admin.customers.table.index', $this->has_locale);

        // Build Form tìm kiếm
        $listdata->search('name', 'Tên', 'string');
        $listdata->search('phone', 'Điện thoại', 'string');
        $listdata->search('email', 'Email', 'string');
        $listdata->search('address', 'Địa chỉ', 'string');
        $listdata->search('created_at', 'Ngày tạo', 'range');
        $listdata->search('status', 'Trạng thái', 'array', config('app.status'));
        $listdata->searchBtn('Export', route('admin.customers.exports'), 'primary', 'fas fa-file-excel');
        // Build các button hành động
        $listdata->btnAction('status', 1, __('Table::table.active'), 'success', 'fas fa-edit');
        $listdata->btnAction('status', 0, __('Table::table.no_active'), 'info', 'fas fa-window-close');
        $listdata->btnAction('delete', -1, __('Table::table.trash'), 'danger', 'fas fa-trash');
        // Build bảng
        $listdata->add('name', 'Tên', 1);
        $listdata->add('phone', 'Điện thoại', 1);
        $listdata->add('email', 'Email', 1);
        $listdata->add('address', 'Địa chỉ', 1);
        $listdata->add('', 'Thời gian', 0, 'time');
        $listdata->add('status', 'Trạng thái', 1, 'status');
        $listdata->add('', 'Sửa', 0, 'edit');
        $listdata->add('', 'Xóa', 0, 'delete');

        // Trả về views
        return $listdata->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $form = new Form;
        $form->text('name', '', 1, 'Tên');
        $form->text('phone', '', 1, 'Điện thoại');
        $form->text('email', '', 0, 'Email');
        $form->text('address', '', 0, 'Địa chỉ');
        $form->radio('status', 1, 'Trạng thái', config('app.status'));
        $form->action('add');
        return $form->render('create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $requests)
    {
        // Xử lý validate
        validateForm($requests, 'name', 'Tiêu đề không được để trống.');
        validateForm($requests, 'phone', 'Điện thoại không được để trống.');
        // Các giá trị mặc định
        $status = 0;
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        // Chuẩn hóa lại dữ liệu
        // Thêm vào DB
        $created_at = $created_at ?? date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $compact = compact('name','phone','email','address','status','created_at','updated_at');
        $id = $this->models->createRecord($requests, $compact, $this->has_seo, $this->has_locale);
        // Điều hướng
        dd(route('admin.'.$this->table_name.'.'.$redirect, $id));
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $id))->with([
            'type' => 'success',
            'message' => __('Core::admin.create_success')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        // Dẽ liệu bản ghi hiện tại
        $data_edit = $this->models->where('id', $id)->first();
        // Khởi tạo form
        $form = new Form;
        $form->text('name', $data_edit->name, 1, 'Tên');
        $form->text('phone', $data_edit->phone, 1, 'Điện thoại');
        $form->text('email', $data_edit->email, 0, 'Email');
        $form->text('address', $data_edit->address, 0, 'Địa chỉ');
        $form->radio('status', 1, 'Trạng thái', config('app.status'));
        $form->action('edit');
        return $form->render('edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $requests
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $requests, $id) {
        // Xử lý validate
        validateForm($requests, 'name', 'Tiêu đề không được để trống.');
        validateForm($requests, 'phone', 'Điện thoại không được để trống.');
        // Lấy bản ghi
        $data_edit = $this->models->where('id', $id)->first();
        // Các giá trị mặc định
        $status = 0;
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        // Chuẩn hóa lại dữ liệu
        // Các giá trị thay đổi
        $updated_at = date('Y-m-d H:i:s');
        $compact = compact('name','phone','email','address','status','updated_at');
        // Cập nhật tại database
        $this->models->updateRecord($requests, $id, $compact, $this->has_seo);
        // Điều hướng
        return redirect(route('admin.'.$this->table_name.'.'.$redirect, $id))->with([
            'type' => 'success',
            'message' => __('Core::admin.update_success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function exports(Request $requests)
    {
        // Đưa mảng về các biến có tên là các key của mảng
        extract($requests->all(), EXTR_OVERWRITE);
        // Lấy dữ liệu được bắt theo bộ lọc
        $data_exports = $this->models::query();
        
        // Tên
        if (isset($name) && $name != '') {
            $data_exports = $data_exports->where('name', 'LIKE', '%'.$name.'%');
        }
        // Điện thoại
        if (isset($phone) && $phone != '') {
            $data_exports = $data_exports->where('phone', 'LIKE', '%'.$phone.'%');
        }
        // Email
        if (isset($email) && $email != '') {
            $data_exports = $data_exports->where('email', 'LIKE', '%'.$email.'%');
        }
        // Nội dung
        if (isset($address) && $address != '') {
            $data_exports = $data_exports->where('address', 'LIKE', '%'.$address.'%');
        }
        // lọc ngày
        if($created_at_end != '' && $created_at_start != '') {
            $data_exports = $data_exports->where('created_at','>',$created_at_start);
            $data_exports = $data_exports->where('created_at','<',$created_at_end);
        }
        // lọc trạng thái
        if (isset($status) && $status != '') {
            $data_query = $data_query->where('status',$status);
        }

        $data_exports = $data_exports->where('status', '<>', -1)->get();

        // Mảng export
        $data = [
            'file_name' => 'customers-'.time(),
            'fields' => [
                __('Tên'),
                __('Điện thoại'),
                __('Email'),
                __('Địa chỉ'),
                __('Thời gian'),
                __('Trạng thái'),
            ],
            'data' => [
                // 
            ]
        ];
        // Foreach lấy mảng data
        $status = config('app.status');
        foreach ($data_exports as $key => $value) {
            $data['data'][] = [
                $value->name,
                $value->phone,
                $value->email,
                $value->address,
                $value->getTime(),
                $status[$value->status] ?? '',
            ];
        }
        return \Excel::download(new \Sudo\Base\Export\GeneralExports($data), $data['file_name'].'.xlsx');
    }

}
