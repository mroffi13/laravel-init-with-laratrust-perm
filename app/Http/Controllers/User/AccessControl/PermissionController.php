<?php

namespace App\Http\Controllers\User\AccessControl;

use App\Helpers\API;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public $limit = 25;
    protected $permissionService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->permissionService = new PermissionService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userLogin = Auth::user();
        if (!$userLogin->isAbleTo('read-acl')) {
            Helper::sessionAlert(
                'Anda tidak memiliki akses [Read Acl]. Mohon hubungi administrator!',
                'alert alert-warning',
                'warning'
            );
            return redirect('/home');
        }

        $theaders = [
            '#',
            'Name',
            'Created By',
            'Created At',
            'Updated By',
            'Updated At',
            'Action'
        ];

        return view('user.acl.permission.index', compact('theaders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $userLogin = Auth::user();
        if (!$userLogin->isAbleTo('create-acl')) {
            Helper::sessionAlert(
                'Anda tidak memiliki akses [Create Acl]. Mohon hubungi administrator!',
                'alert alert-warning',
                'warning'
            );
            return redirect('/access-control/permissions');
        }

        $actions = ['Create', 'Read', 'Update', 'Delete'];

        return view('user.acl.permission.edit', compact('actions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $return = [
            'error' => false,
            'response' => null,
            'message' => null,
            'redirect' => url('/access-control/permissions'),
        ];

        try {
            $userLogin = Auth::user();
            // check permission
            if (!$userLogin->isAbleTo('create-acl')) {
                $return['error'] = true;
                $return['message'] = 'Anda tidak memiliki akses [Create Acl]. Mohon hubungi administrator!';
                return response()->json($return, 200);
            }

            $action_count = !empty($request->action) ? count($request->action) : 0;
            $send_count = 0;

            $valid = [
                'name' => ['required', 'string', 'max:255'],
                'action' => ['required', 'array'],
            ];

            $validator = Validator::make($request->all(), $valid);
            if ($validator->fails()) {
                $return['error'] = true;
                $errors = json_decode(json_encode($validator->messages()));
                $return['response']['errors'] = view('partials.error_validation', compact('errors'))->render();
                return response()->json($return);
            }

            $message = [];
            if (!empty($request->action)) {
                $errors = [];
                foreach ($request->action as $action) {
                    if (!empty($request->name)) {
                        $new_request = collect([]);
                        $new_request->name = $action . ' ' . $request->name;
                        // send api
                        $data = $this->permissionService->store($new_request);
                        // dd($params);
                        if (!empty($data['permission'])) {
                            $send_count++;
                            $message[] = $data['message'];
                        } else
                            $errors[$action] = [$data['error']];
                    }
                }

                if (!empty($errors)) {
                    $return['error'] = true;
                    $errors = json_decode(json_encode($errors));
                    $return['response']['errors'] = view('partials.error_validation', compact('errors'))->render();
                }
            }

            $message = !empty($message) ? implode(', ', $message) : '';
            // dd($response);
            if (!empty($action_count) && $action_count == $send_count)
                Helper::sessionAlert(
                    $message,
                    'swal-alert top-end',
                    'success'
                );
            // dump($response);
            if (!empty($data['permission']))
                $return['response'] = $data;
            if (!empty($message)) {
                if (!empty($data['error']))
                    $return['error'] = true;
                $return['message'] = $message;
            }

            return response()->json($return);
        } catch (\Exception $e) {
            $return['error'] = true;
            $return['message'] = $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine();
            return response()->json($return, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // check permission
        $userLogin = Auth::user();
        if (!$userLogin->isAbleTo('read-acl')) {
            Helper::sessionAlert(
                'Anda tidak memiliki akses [Read Acl]. Mohon hubungi administrator!',
                'alert alert-warning',
                'warning'
            );
            return redirect('/access-control/permissions');
        }

        // get data dari service
        $data = $this->permissionService->show($id);
        // end get user by id

        // check error
        if (!empty($data['error'])) {
            Helper::sessionAlert(
                $data['error'],
                'alert alert-warning',
                'warning'
            );
            return redirect('/access-control/permissions');
        }

        $permission = $data['permission'];

        return view('user.acl.permission.detail', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        // check permission
        $userLogin = Auth::user();
        if (!$userLogin->isAbleTo('update-acl')) {
            Helper::sessionAlert(
                'Anda tidak memiliki akses [Update Acl]. Mohon hubungi administrator!',
                'alert alert-warning',
                'warning'
            );
            return redirect('/access-control/permissions');
        }

        // get data dari service
        $data = $this->permissionService->show($id);
        // end get user by id

        // check error
        if (!empty($data['error'])) {
            Helper::sessionAlert(
                $data['error'],
                'alert alert-warning',
                'warning'
            );
            return redirect('/access-control/permissions');
        }

        $permission = $data['permission'];

        return view('user.acl.permission.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $return = [
            'error' => false,
            'response' => null,
            'message' => null,
            'redirect' => url('/access-control/permissions'),
        ];

        try {
            // check permission
            $userLogin = Auth::user();
            if (!$userLogin->isAbleTo('update-acl')) {
                $return['error'] = true;
                $return['message'] = 'Anda tidak memiliki akses [Update Acl]. Mohon hubungi administrator!';
                return response()->json($return, 200);
            }

            try {
                $id = decrypt($id);
            } catch (\Exception $e) {
                $return['error'] = true;
                $return['message'] = 'ID Salah!';
                return response()->json($return, 200);
            }

            $valid = [
                'name' => ['required', 'string', 'max:255'],
            ];

            $validator = Validator::make($request->all(), $valid);
            if ($validator->fails()) {
                $return['error'] = true;
                $errors = json_decode(json_encode($validator->messages()));
                $return['response']['errors'] = view('partials.error_validation', compact('errors'))->render();
                return response()->json($return);
            }

            // kirim data ke service permission
            $data = $this->permissionService->update($request, $id);
            // end kirim data
            // dd($data);
            // set jika berhasil diupdate
            if (!empty($data['permission']))
                Helper::sessionAlert(
                    $data['message'],
                    'swal-alert top-end',
                    'success'
                );

            if (!empty($data['permission']))
                $return['response'] = $data;
            if (!empty($data['error'])) {
                $return['error'] = true;
                $return['message'] = $data['error'];
            }

            return response()->json($return);
        } catch (\Exception $e) {
            $return['error'] = true;
            $return['message'] = $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine();
            return response()->json($return, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        $return = [
            'error' => false,
            'response' => null,
            'message' => null,
            'redirect' => url('/access-control/permissions'),
        ];

        try {
            try {
                $id = decrypt($id);
            } catch (\Exception $e) {
                $return['error'] = true;
                $return['message'] = 'ID Salah!';
                return response()->json($return, 200);
            }

            // check permission
            $userLogin = Auth::user();
            if (!$userLogin->isAbleTo('delete-acl')) {
                $return['error'] = true;
                $return['message'] = 'Anda tidak memiliki akses [Delete Acl]. Mohon hubungi administrator!';
                return response()->json($return, 200);
            }

            $data = $this->permissionService->destroy($id);

            if (!empty($data['permission']))
                $return['response'] = $data;
            if (!empty($data['message'])) {
                if (!empty($data['error']))
                    $return['error'] = true;
                $return['message'] = $data['message'];
            }

            return response()->json($return);
        } catch (\Exception $e) {
            $return['error'] = true;
            $return['message'] = $e->getMessage() . ' File: ' . $e->getFile() . ' Line: ' . $e->getLine();
            return response()->json($return, 200);
        }
    }

    public function getPermissionList(Request $request)
    {
        $return = [
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        ];

        try {
            // dd($request->all());
            $userLogin = Auth::user();
            if (!$userLogin->isAbleTo('read-acl')) {
                $return['error'] = 'Anda tidak memiliki akses [Read Acl]. Mohon hubungi administrator!';
                return response()->json($return, 200);
            }

            if (!empty($request->length))
                $this->limit = $request->length;
            $page = !empty($request->start) ? ($request->start / $this->limit) + 1 : 1;
            $number = $request->start + 1;
            $order = $request->order[0];
            $column = $order['column'];
            $sort_by = $request->columns[$column]['data'];
            $sort = $order['dir'];

            $new_request = collect([]);
            $new_request->page = $page;
            $new_request->pageSize = $this->limit;
            $new_request->keyword = addcslashes($request->search['value'], "'");
            $new_request->filters = [];
            $new_request->sort = $sort;
            $new_request->sort_by = $sort_by;


            $new_request->filters = !empty($new_request->filters) ? Helper::maybe_serialize($new_request->filters) : '';
            // dump($new_request);
            $data = $this->permissionService->list($new_request);
            // dd($data);
            if (!isset($data['error'])) {
                $return['recordsTotal'] = $return['recordsFiltered'] = $data['records'];

                if (!empty($data['permissions'])) {
                    foreach ($data['permissions'] as $permission) {
                        $created_at = $permission->created_at;
                        $updated_at = $permission->updated_at;
                        $permission->number = $number;
                        $number++;
                        $permission->created_at = date('d M y H:i:s', strtotime($created_at));
                        $permission->updated_at = date('d M y H:i:s', strtotime($updated_at));

                        $permission->url = '/access-control/permissions/';
                        $permission->action_html = view('partials.action', [
                            'data' => $permission,
                            'buttons' => Helper::button_defaut($permission, 'acl'),
                        ])->render();
                    }

                    $return['data'] = $data['permissions'];
                }
            } else
                $return['error'] = $data['error'];

            return response()->json($return);
        } catch (\Exception $e) {
            $return['error'] = $e->getMessage() . '. Line: ' . $e->getLine();
            return response()->json($return);
        }
    }

    public function getSelect2Option(Request $request)
    {
        $return = [
            'error' => 'not found'
        ];
        try {
            // dd($request->all());
            $userLogin = Auth::user();
            if (!$userLogin->isAbleTo('read-acl')) {
                $return['error'] = 'Anda tidak memiliki akses [Read Acl]. Mohon hubungi administrator!';
                return response()->json($return, 200);
            }

            $page = !empty($request->page) ? $request->page : 1;
            $search = !empty($request->search) ? $request->search : '';

            $data_req = [
                'page' => $page,
                'pageSize' => $this->limit,
                'keyword' => addcslashes($search, "'"),
                'filters' => [],
            ];

            $data_req['filters'] = !empty($data_req['filters']) ? Helper::maybe_serialize($data_req['filters']) : '';

            // Create new Req
            $new_request = new Request();
            $new_request->request->add($data_req);

            $data = $this->permissionService->getSelect2Resource($new_request);
            $code = 404;
            if (!empty($data['data']['results'])) {
                $return = $data['data'];
                if (!empty($request->result_html))
                    $return['result_html'] = view(
                        'partials.option',
                        [
                            'datas' => !empty($return['results']) ? json_decode(json_encode($return['results'])) : [],
                            'selected' => !empty($request->selected) ? $request->selected : []
                        ]
                    )->render();
                $code = 200;
            }

            return response()->json($return, $code);
        } catch (\Exception $e) {
            $return['error'] = $e->getMessage();
            return response()->json($return, 404);
        }
    }
}
