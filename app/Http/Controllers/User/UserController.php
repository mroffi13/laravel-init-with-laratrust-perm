<?php

namespace App\Http\Controllers\User;

use App\Helpers\API;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public $limit = 25;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.dashboard');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!Helper::isAbleTo($request, 'read-users'))
        {
            Helper::sessionAlert(
                'You don\'t have permission [Read Users]. Please contact administrator!', 
                'alert alert-warning', 
                'warning'
            );
            return redirect('/home');
        }

        $theaders = [
            '#',
            'Name',
            'Email',
            'Created By',
            'Registered At',
            'Updated By',
            'Updated At',
            'Status',
            'Action'
        ];

        return view('user.user.index', compact('theaders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        if(!Helper::isAbleTo($request, 'create-users'))
        {
            Helper::sessionAlert(
                'You don\'t have permission [Create Users]. Please contact administrator!', 
                'alert alert-warning',
                'warning'
            );
            return redirect('/users');
        }

        return view('user.user.edit');
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
            'redirect' => url('/users'),
        ];

        try 
        {
            // check permission
            if(!Helper::isAbleTo($request, 'create-users'))
            {
                $return['error'] = true;
                $return['message'] = 'You don\'t have permission [Create Users]. Please contact administrator!';
                return response()->json($return, 200);
            }

            // upload img
            if($request->hasFile('img_profile'))
                $img_profile = Helper::uploadFile($request->file('img_profile'), 'img_profile');

            // set param
            $metas = [];
            if(!empty($request->metas))
                $metas = $request->metas;

            if(!empty($img_profile))
                $metas['img_profile'] = Helper::maybe_serialize($img_profile);
            $params = [
                'endpoint' => 'register',
                'form_request' => [
                    'name' => !empty($request->name) ? $request->name : null,
                    'email' => !empty($request->email) ? $request->email : null,
                    'phone' => !empty($request->phone) ? $request->phone : null,
                    'role' => !empty($request->role) ? $request->role : null,
                    'password' => !empty($request->password) ? $request->password : null,
                    'password_confirmation' => !empty($request->password_confirmation) ? $request->password_confirmation : null,
                    'outlet' => !empty($request->outlet) ? $request->outlet : null,
                    'metas' => !empty($metas) ? $metas : null
                ],
                'headers' => ['Authorization' => 'Bearer ' . $request->access_token]
            ];
    
            // send api
            $response = API::post($params);
            $response = json_decode($response);
            // dd($response);
            if($response->code == 200 && !empty($response->data->user))
                Helper::sessionAlert(
                    $response->message, 
                    'swal-alert top-end',
                    'success'
                );

            // jika ada error validation
            if(!empty($response->data->errors))
            {
                $errors = $response->data->errors;
                $return['response']['errors'] = view('partials.error_validation', compact('errors'))->render();
            }
            elseif(!empty($response->data))
                $return['response'] = $response->data;
            if(!empty($response->message))
            {
                if($response->code !== 200)
                    $return['error'] = true;
                $return['message'] = $response->message;
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // check permission
        if(!Helper::isAbleTo($request, 'read-users'))
        {
            Helper::sessionAlert(
                'You don\'t have permission [Read Users]. Please contact administrator!', 
                'alert alert-warning',
                'warning'
            );
            return redirect('/users');
        }

        // start get user by id
        $params = [
            'endpoint' => 'users/'. $id,
            'get_request' => [],
            'headers' => ['Authorization' => 'Bearer ' . $request->access_token]
        ];

        $response = API::get($params);
        $response = json_decode($response);
        // end get user by id

        // check error
        if($response->code !== 200)
        {
            Helper::sessionAlert(
                $response->message, 
                'alert alert-warning',
                'warning'
            );
            return redirect('/users');
        }
        
        //convert data
        $user = $response->data->user;

        if(!empty($user->metas))
            $user->metas = Helper::convertMetas($user->metas);
        if(!empty($user->image))
            $user->image = Helper::maybe_unserialize($user->image->meta_value);
        //end convert data

        return view('user.user.detail', compact('user'));
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
        if(!Helper::isAbleTo($request, 'update-users'))
        {
            Helper::sessionAlert(
                'You don\'t have permission [Update Users]. Please contact administrator!', 
                'alert alert-warning',
                'warning'
            );
            return redirect('/users');
        }

        // start get user by id
        $params = [
            'endpoint' => 'users/'. $id,
            'get_request' => [],
            'headers' => ['Authorization' => 'Bearer ' . $request->access_token]
        ];

        $response = API::get($params);
        $response = json_decode($response);
        // end get user by id

        // check error
        if($response->code !== 200)
        {
            Helper::sessionAlert(
                $response->message, 
                'alert alert-warning',
                'warning'
            );
            return redirect('/users');
        }
        
        //convert data
        $user = $response->data->user;

        if(!empty($user->metas))
            $user->metas = Helper::convertMetas($user->metas);
        if(!empty($user->image))
            $user->image = Helper::maybe_unserialize($user->image->meta_value);
        //end convert data

        return view('user.user.edit', compact('user'));
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
            'redirect' => !empty($request->profile) ? url('/profile') : url('/users'), // check source route if from profile redirect profile else users
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
            if(!Helper::isAbleTo($request, empty($request->profile) ? 'update-users' : 'update-profile'))
            {
                $return['error'] = true;
                $return['message'] = 'You don\'t have permission ['. empty($request->profile) ? 'Update Users' : 'Update Profile' .']. Please contact administrator!';
                return response()->json($return, 200);
            }

            // upload img
            if($request->hasFile('img_profile'))
                $img_profile = Helper::uploadFile($request->file('img_profile'), 'img_profile');

            // set param
            $metas = [];
            if(!empty($request->metas))
                $metas = $request->metas;

            if(!empty($img_profile))
                $metas['img_profile'] = Helper::maybe_serialize($img_profile);
            $params = [
                'endpoint' => 'users/'.$id,
                'form_request' => [
                    'user' => [
                        'name' => !empty($request->name) ? $request->name : null,
                        'email' => !empty($request->email) ? $request->email : null,
                        'phone' => !empty($request->phone) ? $request->phone : null,
                        'role' => !empty($request->role) ? $request->role : null,
                        'pin' => !empty($request->pin) ? $request->pin : '',
                        'password' => !empty($request->password) ? $request->password : null,
                        'password_confirmation' => !empty($request->password_confirmation) ? $request->password_confirmation : null,
                        'outlet' => !empty($request->outlet) ? $request->outlet : null,
                        'metas' => !empty($metas) ? $metas : null,
                        'status' => !empty($request->status) ? 1 : 0
                    ]
                ],
                'headers' => ['Authorization' => 'Bearer ' . $request->access_token]
            ];
    
            // send api
            $response = API::put($params);
            $response = json_decode($response);

            if($response->code == 200 && !empty($response->data->user))
                Helper::sessionAlert(
                    $response->message, 
                    'swal-alert top-end',
                    'success'
                );

            // jika ada error validation
            if(!empty($response->data->errors))
            {
                $errors = $response->data->errors;
                $return['response']['errors'] = view('partials.error_validation', compact('errors'))->render();
            }
            elseif(!empty($response->data))
                $return['response'] = $response->data;
            if(!empty($response->message))
            {
                if($response->code !== 200)
                    $return['error'] = true;
                $return['message'] = $response->message;
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
            'redirect' => url('/users'),
        ];

        try 
        {
            // check permission
            try {
                $id = decrypt($id);
            } 
            catch (\Exception $e) 
            {
                $return['error'] = true;
                $return['message'] = 'Wrong id!';
                return response()->json($return, 200);
            }
            
            if(!Helper::isAbleTo($request, 'delete-users'))
            {
                $return['error'] = true;
                $return['message'] = 'You don\'t have permission [Delete Users]. Please contact administrator!';
                return response()->json($return, 200);
            }

            $params = [
                'endpoint' => 'users/'.$id,
                'form_request' => [
                ],
                'headers' => ['Authorization' => 'Bearer ' . $request->access_token]
            ];
    
            // send api
            $response = API::delete($params);
            $response = json_decode($response);

            if(!empty($response->data))
                $return['response'] = $response->data;
            if(!empty($response->message))
            {
                if($response->code !== 200)
                    $return['error'] = true;
                $return['message'] = $response->message;
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

    public function getUserList(Request $request)
    {
        $return = [
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        ];

        try 
        {
            // dd($request->all());
            if(!Helper::isAbleTo($request, 'read-users'))
            {
                $return['error'] = 'You don\'t have permission [Read Users]. Please contact administrator!';
                return response()->json($return, 200);
            }

            $access_token = $request->access_token;

            if(!empty($request->length))
                $this->limit = $request->length;
            $page = !empty($request->start) ? ($request->start/$this->limit)+1 : 1;
            $number = $request->start+1;
            $order = $request->order[0];
            $column = $order['column'];
            $sort_by = $request->columns[$column]['data'];
            $sort = $order['dir'];

            $param = [
                'endpoint' => 'users',
                'get_request' => [
                    'page' => $page,
                    'pageSize' => $this->limit,
                    'keyword' => addcslashes($request->search['value'], "'"),
                    'filters' => [],
                    'sort' => $sort,
                    'sort_by' => $sort_by
                ],
                'headers' => [
                    'Authorization' => 'Bearer '. $access_token
                ]
            ];

            $param['get_request']['filters'] = !empty($param['get_request']['filters']) ? Helper::maybe_serialize($param['get_request']['filters']) : '';

            $response = API::get($param);
            $response = json_decode($response);

            if($response->code == 200)
            {
                $return['recordsTotal'] = $return['recordsFiltered'] = $response->data->records;

                if(!empty($response->data->users))
                {
                    foreach ($response->data->users as $user) 
                    {   
                        $created_at = $user->created_at;
                        $updated_at = $user->updated_at;
                        $user->number = $number;
                        $number++;
                        $user->created_at = date('d M y H:i:s', strtotime($created_at));
                        $user->updated_at = date('d M y H:i:s', strtotime($updated_at));
                        switch ($user->status) 
                        {
                            case 0:
                                $user->status = Helper::badge($user->status_label, 'warning');
                                break;
                            
                            default:
                                $user->status = Helper::badge($user->status_label, 'success');
                                break;
                        }

                        $user->url = '/users/';
                        $user->action_html = view('partials.action', [
                            'data' => $user,
                            'buttons' => Helper::button_defaut($user, 'users'),
                        ])->render();
                    }

                    $return['data'] = $response->data->users;
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
}
