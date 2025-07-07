<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Libraries
use App\Libraries\Helper;

// Models
use App\Models\page;
use App\Models\nav_menu;
use App\Models\office;
use App\Models\social_media;
use App\Models\faq;

class SiteController extends Controller
{
    private function get_faq()
    {
        $data = faq::where('status', 1)
            ->orderBy('level')
            ->orderBy('parent_id')
            ->orderBy('ordinal')
            ->get();

        if (!isset($data[0])) {
            // NO DATA
            return;
        }

        $array_object = $data;
        $params_child = [
            'id',
            'text_1',
            'text_2',
            'level',
            'parent_id'
        ];
        $parent = 'level';
        $data_per_level = Helper::generate_parent_child_data_array($array_object, $parent, $params_child);

        $arr = [];
        foreach ($data_per_level as $level => $menulist) {
            foreach ($menulist as $menu) {
                // level_id : lvl1_id2
                $var_name = 'lvl' . $level . '_id' . $menu['id'];

                $parent_level = $menu['level'] - 1;
                $var_name_parent = 'lvl' . $parent_level . '_id' . $menu['parent_id'];

                // convert array to object
                $obj = new \stdClass();
                foreach ($menu as $key => $value) {
                    $obj->$key = $value;
                }
                // dd($menu, $obj);

                if (isset($arr[$var_name_parent])) {
                    $var_name_sub = 'level_' . $menu['level'];
                    $arr[$var_name_parent]->$var_name_sub[] = $obj;
                }
                $arr[$var_name] = $obj;
            }
        }

        $data_faq = [];
        foreach ($arr as $key => $value) {
            if (Helper::is_contains('lvl1', $key)) {
                $data_faq[] = $value;
            }
        }

        return $data_faq;
    }

    public function index()
    {
        $locale = app()->getLocale();
        return redirect()->route('web.page', ['lang' => $locale, 'slug' => 'home']);
    }

    public function page($lang, $slug, Request $request)
    {
        $slug_safe = Helper::validate_input_text($slug);

        $query = Page::where('slug', $slug_safe);

        if (!$request->preview) {
            $query->where('status', 1);
        }

        $data = $query->first();

        if (!$data) {
            dd("Page not found: " . $slug_safe);
            return abort(404);
        }

        $faq = null;
        if ($slug_safe == 'prakerja') {
            $faq = $this->get_faq();
        }

        return view('web.page', compact('data', 'faq'));
    }

    public function faq()
    {
        $data = $this->get_faq();

        return view('web.faq', compact('data'));
    }
}