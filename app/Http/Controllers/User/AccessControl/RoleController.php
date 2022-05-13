<?php

namespace App\Http\Controllers\User\AccessControl;

use App\Helpers\API;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public $limit = 25;
    protected $roleService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->roleService = new RoleService();
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

        return view('user.acl.role.index', compact('theaders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userLogin = Auth::user();
        if (!$userLogin->isAbleTo('create-acl')) {
            Helper::sessionAlert(
                'Anda tidak memiliki akses [Create Acl]. Mohon hubungi administrator!',
                'alert alert-warning',
                'warning'
            );
            return redirect('/access-control/roles');
        }

        return view('user.acl.role.edit');
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
            'redirect' => url('/access-control/roles'),
        ];

        try 
        {
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
                'permissions' => ['required', 'array'],
            ];
    
            $validator = Validator::make($request->all(), $valid);
            if ($validator->fails()) {
                $return['error'] = true;
                $errors = json_decode(json_encode($validator->messages()));
                $return['response']['errors'] = view('partials.error_validation', compact('errors'))->render();
                return response()->json($return);
            }

            $data = $this->roleService->store($request);

            if($data['role'])
                Helper::sessionAlert(
                    $data['message'], 
                    'swal-alert top-end',
                    'success'
                );
            // dump($response);
            if(!empty($data['role']))
                $return['response'] = $data;
            if(!empty($data['error']))
                $return['error'] = true;
            $return['message'] = !empty($data['message']) ? $data['message'] : $data['error'];

            return response()->json($return);
        } 
        catch (\Exception $e) 
        {
            $return['error'] = true;
            $return['message'] = $e->getMessage() . ' File: '.$e->getFile(). ' Line: '.$e->getLine();
            return response()->json($return, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        // check permission
        $userLogin = Auth::user();
        if(!$userLogin->isAbleTo('read-acl'))
        {
            Helper::sessionAlert(
                'You don\'t have permission [Read Acl]. Please contact administrator!', 
                'alert alert-warning',
                'warning'
            );
            return redirect('/access-control/roles');
        }

        // get data dari service
        $data = $this->roleService->show($id);
        // end get user by id

        // check error
        if (!empty($data['error'])) {
            Helper::sessionAlert(
                $data['error'],
                'alert alert-warning',
                'warning'
            );
            return redirect('/access-control/roles');
        }

        $role = $data['role'];

        return view('user.acl.role.detail', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $userLogin = Auth::user();
        if (!$userLogin->isAbleTo('update-acl')) {
            Helper::sessionAlert(
                'Anda tidak memiliki akses [Update Acl]. Mohon hubungi administrator!',
                'alert alert-warning',
                'warning'
            );
            return redirect('/access-control/roles');
        }

        // get data dari service
        $data = $this->roleService->show($id);
        // end get user by id

        // check error
        if (!empty($data['error'])) {
            Helper::sessionAlert(
                $data['error'],
                'alert alert-warning',
                'warning'
            );
            return redirect('/access-control/roles');
        }

        $role = $data['role'];

        return view('user.acl.role.edit', compact('role'));
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
            'redirect' => url('/access-control/roles'),
        ];

        try 
        {
            try {
                $id = decrypt($id);
            } catch (\Exception $e) {
                $return['error'] = true;
                $return['message'] = 'Wrong id!';
                return response()->json($return, 200);
            }
            // check permission
            $userLogin = Auth::user();
            if (!$userLogin->isAbleTo('update-acl')) {
                $return['error'] = true;
                $return['message'] = 'Anda tidak memiliki akses [Update Acl]. Mohon hubungi administrator!';
                return response()->json($return, 200);
            }

            $valid = [
                'name' => ['required', 'string', 'max:255'],
                'permissions' => ['required', 'array'],
            ];
    
            $validator = Validator::make($request->all(), $valid);
            if ($validator->fails()) {
                $return['error'] = true;
                $errors = json_decode(json_encode($validator->messages()));
                $return['response']['errors'] = view('partials.error_validation', compact('errors'))->render();
                return response()->json($return);
            }

            // kirim data ke service permission
            $data = $this->roleService->update($request, $id);
            // end kirim data
            // dd($data);
            // set jika berhasil diupdate
            if (!empty($data['role']))
                Helper::sessionAlert(
                    $data['message'],
                    'swal-alert top-end',
                    'success'
                );

            if (!empty($data['role']))
                $return['response'] = $data;
            if (!empty($data['error'])) {
                $return['error'] = true;
                $return['message'] = $data['error'];
            }

            return response()->json($return);
        } 
        catch (\Exception $e) 
        {
            $return['error'] = true;
            $return['message'] = $e->getMessage() . ' File: '.$e->getFile(). ' Line: '.$e->getLine();
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
            'redirect' => url('/access-control/roles'),
        ];

        try 
        {
            try {
                $id = decrypt($id);
            } 
            catch (\Exception $e) 
            {
                $return['error'] = true;
                $return['message'] = 'Wrong id!';
                return response()->json($return, 200);
            }
            
            // check permission
            $userLogin = Auth::user();
            if (!$userLogin->isAbleTo('delete-acl')) {
                $return['error'] = true;
                $return['message'] = 'Anda tidak memiliki akses [Delete Acl]. Mohon hubungi administrator!';
                return response()->json($return, 200);
            }

            $data = $this->roleService->destroy($id);

            if (!empty($data['role']))
                $return['response'] = $data;
            if (!empty($data['error']))
                $return['error'] = true;
            $return['message'] = !empty($data['message']) ? $data['message'] : $data['error'];

            return response()->json($return);
        } 
        catch (\Exception $e) 
        {
            $return['error'] = true;
            $return['message'] = $e->getMessage() . ' File: '.$e->getFile(). ' Line: '.$e->getLine();
            return response()->json($return, 200);
        }
    }

    public function getRoleList(Request $request)
    {
        $return = [
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        ];

        try 
        {
            // dd($request->all());
            $userLogin = Auth::user();
            if (!$userLogin->isAbleTo('read-acl')) {
                $return['error'] = 'Anda tidak memiliki akses [Read Acl]. Mohon hubungi administrator!';
                return response()->json($return, 200);
            }

            if(!empty($request->length))
                $this->limit = $request->length;
            $page = !empty($request->start) ? ($request->start/$this->limit)+1 : 1;
            $number = $request->start+1;
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
            $data = $this->roleService->list($new_request);

            if(!isset($data['error']))
            {
                $return['recordsTotal'] = $return['recordsFiltered'] = $data['records'];

                if(!empty($data['roles']))
                {
                    foreach ($data['roles'] as $role) 
                    {   
                        $created_at = $role->created_at;
                        $updated_at = $role->updated_at;
                        $role->number = $number;
                        $number++;
                        $role->created_at = date('d M y H:i:s', strtotime($created_at));
                        $role->updated_at = date('d M y H:i:s', strtotime($updated_at));

                        $role->url = '/access-control/roles/';
                        $role->action_html = view('partials.action', [
                            'data' => $role,
                            'buttons' => Helper::button_defaut($role, 'acl'),
                        ])->render();
                    }

                    $return['data'] = $data['roles'];
                }
            }

            return response()->json($return);
        } 
        catch (\Exception $e) 
        {
            $return['error'] = $e->getMessage();
            return response()->json($return);
        }
    }

    public function getSelect2Option(Request $request)
    {
        $return = [
            'error' => 'not found'
        ];
        try 
        {
            // dd($request->all());
            if(!Helper::isAbleTo($request, 'read-acl'))
            {
                $return['error'] = 'You don\'t have permission [Read Acl]. Please contact administrator!';
                return response()->json($return, 200);
            }

            $access_token = $request->access_token;

            $page = !empty($request->page) ? $request->page : 1;
            $search = !empty($request->search) ? $request->search : '';

            $param = [
                'endpoint' => 'acl/roles/getSelect2Resource',
                'get_request' => [
                    'page' => $page,
                    'pageSize' => $this->limit,
                    'keyword' => addcslashes($search, "'"),
                    'filters' => [],
                ],
                'headers' => [
                    'Authorization' => 'Bearer '. $access_token
                ]
            ];

            $param['get_request']['filters'] = !empty($param['get_request']['filters']) ? Helper::maybe_serialize($param['get_request']['filters']) : '';

            $response = API::get($param);
            $response = json_decode($response);
            $code = 404;
            if($response->code == 200)
            {
                $return = $response->data->data;
                $code = 200;
            }

            return response()->json($return, $code);
        } 
        catch (\Exception $e) 
        {
            $return['error'] = $e->getMessage();
            return response()->json($return, 404);
        }
    }
}
