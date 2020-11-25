<?php

namespace App\Http\Controllers;

use App\Area;
use App\TimeRange;
use Illuminate\Http\Request;

class TimeRangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $times = TimeRange::all();
        return view('admin.times', compact('times'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->filled('from') && $request->filled('to')){
            $time = new TimeRange();
            $time->title    =   $request->input('from').' - '.$request->input('to');
            $time->from     =   $request->input('from');
            $time->to       =   $request->input('to');
            $time->order       =   $request->input('order');

            $time->save();
            return back()->with('success','Time Range saved');
        }else{
            return  back()->withErrors(['All fields are required']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $area = TimeRange::whereId($request->input('id'))->get();
        echo $area;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function edit(Area $area)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $from   = $request->input('from');
        $to     = $request->input('to');
        $order     = $request->input('order');
        $name   = $from.' - '.$to;
        TimeRange::whereId($id)->update(['title'=>$name,'from'=>$from,'to'=>$to,'order'=>$order]);

        return back()->with('success','Time range updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function destroy(Area $area, $id)
    {
        TimeRange::whereId($id)->delete();
        return back()->with('success','Deleted successfully');
    }
}
