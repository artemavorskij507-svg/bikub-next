<?php
namespace App\Http\Controllers;use App\Services\Localization\LocaleManager;use Illuminate\Http\RedirectResponse;use Illuminate\Http\Request;
class LocaleController{public function __invoke(Request $request,LocaleManager $manager):RedirectResponse{$data=$request->validate(['locale'=>['required','string']]);abort_unless($manager->isSupported($data['locale']),422);$request->session()->put('bikube_locale',$data['locale']);$manager->setLocale($data['locale']);return back();}}
