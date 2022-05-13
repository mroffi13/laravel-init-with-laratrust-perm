<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleService
{
    public function list($request)
    {
        try {
            $returns = [
                'records' => 0,
                'more' => false,
                'roles' => null,
            ];

            $role = new Role;
            $indexes = $role->getIndex();
            $page = !empty($request->page) ? $request->page - 1 : 0;
            $pageSize = !empty($request->pageSize) ? $request->pageSize : 10;
            $keyword = !empty($request->keyword) ? $request->keyword : '';
            $filters = !empty($request->filters) ? json_decode($request->filters, true) : [];
            $date_filters = (!empty($request->date_filter)) ? json_decode($request->date_filter, true) : null;
            $sort = !empty($request->sort) ? $request->sort : 'asc';
            $sort_by = !empty($request->sort_by) ? $request->sort_by : 'created_at';

            $roleQ = Role::select('*');

            if (!empty($filters)) {
                foreach ($filters as $where => $value_in) {
                    if (is_array($value_in))
                        $roleQ->whereIn($where, $value_in);
                    else
                        $roleQ->where($where, $value_in);
                }
            }

            if (!empty($keyword)) {
                $roleQ->where(function ($query) use ($keyword, $indexes) {
                    foreach ($indexes as $src)
                        $query->orWhere($src, 'LIKE', '%' . $keyword . '%');
                });
            }

            if (!empty($date_filters)) {
                foreach ($date_filters as $key => $date) {
                    if (!empty($date['start']) && !empty($date['end'])) {
                        $roleQ->whereBetween($key, [$date['start'], $date['end']]);
                    }
                }
            }

            $returns['records'] = $roleQ->count();

            if (!empty($returns['records'])) {
                $roleQ->orderBy($sort_by, $sort);
                if (!empty($pageSize))
                    $roleQ->offset(($page * $pageSize))->limit($pageSize);

                $roles = $roleQ->get();

                $returns['roles'] = $roles;
                $returns['more'] = (($page + 1) * $pageSize) < $returns['records'];
            }

            return $returns;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dev = 'File: ' . $e->getFile() . '. Line: ' . $e->getLine();
            Log::error($dev);
            return ['error' => $message];
        }
    }

    public function store($request)
    {
        try {
            $login = Auth::user();

            $exist = Role::where('display_name', $request->name)->first();
            if (!empty($exist))
                return ['error' => "Permission {$request->name} sudah ada didatabase."];

            $role = new Role;
            $role->name = str_replace(' ', '_', strtolower($request->name));
            $role->display_name = $request->name;
            if (!empty($role->description)) $role->description = $request->description;
            Helper::insert_log_user($role, $login);
            $role->save();

            if(!empty($request->permissions))
                $role->syncPermissions($request->permissions);

            $role = Role::find($role->id);

            return [
                'role' => $role,
                'message' => "Role {$role->display_name} berhasil dibuat."
            ];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dev = 'File: ' . $e->getFile() . '. Line: ' . $e->getLine();
            Log::error($dev);
            return ['error' => $message];
        }
    }

    public function update($request, $id)
    {
        try {
            $login = Auth::user();

            $role = Role::find($id);
            if(empty($role))
                return ['error' => 'ID Role #'. $id . ' tidak ditemukan!'];

            $exist = Role::where('display_name', $request->name)->where('id', '!=', $id)->first();
            if (!empty($exist))
                return ['error' => "Role {$request->name} sudah ada didatabase."];

            $role->name = str_replace(' ', '_', strtolower($request->name));
            $role->display_name = $request->name;
            if (!empty($role->description)) $role->description = $request->description;
            Helper::insert_log_user($role, $login, 1);
            $role->save();

            $permission = !empty($request->permissions) ? $request->permissions : [];
            $role->syncPermissions($permission);

            $role = Role::find($role->id);

            return [
                'role' => $role,
                'message' => "Role {$role->display_name} berhasil diupdate."
            ];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dev = 'File: ' . $e->getFile() . '. Line: ' . $e->getLine();
            Log::error($dev);
            return ['error' => $message];
        }
    }

    public function show($id)
    {
        try {

            $role = Role::with(['permissions'])->find($id);
            if (empty($role))
                return ['error' => "ID Role #{$id} not found."];

            return [
                'role' => $role,
            ];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dev = 'File: ' . $e->getFile() . '. Line: ' . $e->getLine();
            Log::error($dev);
            return ['error' => $message];
        }
    }

    public function destroy($id)
    {
        try {
            $login = Auth::user();

            $role = Role::find($id);
            if (empty($role))
                return ['error' => "ID Role #{$id} tidak ditemukan."];

            $role->delete();

            return [
                'role' => $role,
                'message' => "Role {$role->display_name} berhasil dihapus!"
            ];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dev = 'File: ' . $e->getFile() . '. Line: ' . $e->getLine();
            Log::error($dev);
            return ['error' => $message];
        }
    }

    public function getSelect2Resource($request)
    {
        try {

            $returns = [
                'records' => 0,
                'data' => [
                    'results' => [],
                    'pagination' => [
                        'more' => false,
                    ]
                ],
            ];

            $role = new Role;
            $indexes = $role->getIndex();
            $page = !empty($request->page) ? $request->page - 1 : 0;
            $pageSize = !empty($request->pageSize) ? $request->pageSize : 10;
            $keyword = !empty($request->keyword) ? $request->keyword : '';
            $sort = !empty($request->sort) ? $request->sort : 'asc';
            $sort_by = !empty($request->sort_by) ? $request->sort_by : 'created_at';

            $roleQ = Role::selectRaw('id, display_name as text');

            if (!empty($keyword)) {
                $roleQ->where(function ($query) use ($keyword, $indexes) {
                    foreach ($indexes as $src)
                        $query->orWhere($src, 'LIKE', '%' . $keyword . '%');
                });
            }

            $returns['records'] = $roleQ->count();

            if (!empty($returns['records'])) {
                $roleQ->orderBy($sort_by, $sort);
                if (!empty($pageSize))
                    $roleQ->offset(($page * $pageSize))->limit($pageSize);

                $roles = $roleQ->get();

                $returns['data']['results'] = $roles;
                $returns['data']['pagination']['more'] = (($page + 1) * $pageSize) < $returns['records'];
            }

            return $returns;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dev = 'File: ' . $e->getFile() . '. Line: ' . $e->getLine();
            Log::error($dev);
            return ['error' => $message];
        }
    }
}
