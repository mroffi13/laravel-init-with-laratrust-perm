<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PermissionService
{
    public function list($request)
    {
        try {
            $returns = [
                'records' => 0,
                'more' => false,
                'permissions' => null,
            ];

            $permission = new Permission;
            $indexes = $permission->getIndex();
            $page = !empty($request->page) ? $request->page - 1 : 0;
            $pageSize = !empty($request->pageSize) ? $request->pageSize : 10;
            $keyword = !empty($request->keyword) ? $request->keyword : '';
            $filters = !empty($request->filters) ? json_decode($request->filters, true) : [];
            $date_filters = (!empty($request->date_filter)) ? json_decode($request->date_filter, true) : null;
            $sort = !empty($request->sort) ? $request->sort : 'asc';
            $sort_by = !empty($request->sort_by) ? $request->sort_by : 'created_at';

            $permissionQ = Permission::select('*');

            if (!empty($filters)) {
                foreach ($filters as $where => $value_in) {
                    if (is_array($value_in))
                        $permissionQ->whereIn($where, $value_in);
                    else
                        $permissionQ->where($where, $value_in);
                }
            }

            if (!empty($keyword)) {
                $permissionQ->where(function ($query) use ($keyword, $indexes) {
                    foreach ($indexes as $src)
                        $query->orWhere($src, 'LIKE', '%' . $keyword . '%');
                });
            }

            if (!empty($date_filters)) {
                foreach ($date_filters as $key => $date) {
                    if (!empty($date['start']) && !empty($date['end'])) {
                        $permissionQ->whereBetween($key, [$date['start'], $date['end']]);
                    }
                }
            }

            $returns['records'] = $permissionQ->count();

            if (!empty($returns['records'])) {
                $permissionQ->orderBy($sort_by, $sort);
                if (!empty($pageSize))
                    $permissionQ->offset(($page * $pageSize))->limit($pageSize);

                $permissions = $permissionQ->get();

                $returns['permissions'] = $permissions;
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

            $exist = Permission::where('display_name', $request->name)->first();
            if (!empty($exist))
                return ['error' => "Permission {$request->name} sudah ada didatabase."];

            $permission = new Permission;
            $permission->name = str_replace(' ', '-', strtolower($request->name));
            $permission->display_name = $request->name;
            if (!empty($permission->description)) $permission->description = $request->description;
            Helper::insert_log_user($permission, $login);
            $permission->save();

            $permission = Permission::find($permission->id);

            return [
                'permission' => $permission,
                'message' => "Permission {$permission->display_name} berhasil dibuat."
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

            $permission = Permission::find($id);
            if(empty($permission))
                return ['error' => 'ID Permission #'. $id . ' tidak ditemukan!'];

            $exist = Permission::where('display_name', $request->name)->where('id', '!=', $id)->first();
            if (!empty($exist))
                return ['error' => "Permission {$request->name} sudah ada didatabase."];

            $permission->name = str_replace(' ', '-', strtolower($request->name));
            $permission->display_name = $request->name;
            if (!empty($permission->description)) $permission->description = $request->description;
            Helper::insert_log_user($permission, $login, 1);
            $permission->save();

            $permission = Permission::find($permission->id);

            return [
                'permission' => $permission,
                'message' => "Permission {$permission->display_name} berhasil diupdate."
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

            $permission = Permission::find($id);
            if (empty($permission))
                return ['error' => "ID Permission #{$id} not found."];

            return [
                'permission' => $permission,
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

            $permission = Permission::find($id);
            if (empty($permission))
                return ['error' => "ID Permission #{$id} tidak ditemukan."];

            $permission->delete();

            return [
                'permission' => $permission,
                'message' => "Permission {$permission->display_name} berhasil dihapus!"
            ];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dev = 'File: ' . $e->getFile() . '. Line: ' . $e->getLine();
            Log::error($dev);
            return ['error' => $message];
        }
    }

    public function getSelect2Resource(Request $request)
    {
        try {
            $login = Auth::user();
            if (!$login->isAbleTo('read-acl'))
                return errorCustomStatus(403);

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

            $roleQ = Role::selectRaw('id, display_name as text')->where('status', '=', 1);

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

            return responses($returns);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $dev = 'File: ' . $e->getFile() . '. Line: ' . $e->getLine();
            Log::error($dev);
            return errorQuery($message);
        }
    }
}
