<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Page;
use Validator;

class PagesEditController extends Controller
{
    public function execute(Page $page, Request $request) {
     // $page = Page::find(id);

     if ($request->isMethod('delete')) {
       $page->delete();
       return redirect('admin')->with('status', 'Страница удалена');
     }


     if ($request->isMethod('post')) {
       $input = $request->except('_token');

       $validator = Validator::make($input, [
         'name'=> 'required|max:255',
         'alias' => 'required|max:255|unique:pages,alias,'.$input['id'],
         'text' => 'required'
       ]);

       if ($validator->fails()) {
         return redirect()
                          ->route('pagesEdit', ['page', $input['id']])
                          ->withErrors($validator);
       }

       if ($request->hasFile('images')) {
         $file = $request->file('images');
         $file->move(publick_path().'/assets/img', $file->getClientOriginalName());
         $input['images'] = $file->getClientOriginalName();

       }

     unset($input['old_images']);
     $page->fill($input);
     if ($page->update()) {
       return redirect('admin')->with('status', 'Страница обновлена');
     }
   }

   $old = $page->toArray();
   if (view()->exists('admin.pages_edit')) {
     $data = [
       'title' => 'Редактирования странице - '.$old['name'],
       'data' => $old
     ];
     return view('admin.pages_edit', $data);
   }


}
}
