{{-- ADD HTML LARGE MODAL - BEGIN --}}
@extends('_template_adm.modal_large')
{{-- LARGE MODAL CONFIG --}}
@section('large_modal_id', 'modal_preview')
@section('large_modal_title', ucwords(lang('file preview', $translations)))
@section('large_modal_content')
    <div class="row">
        {{-- PREVIEW --}}
        <div class="col-lg-8" id="modal_preview_content"></div>
        {{-- DETAILS --}}
        <div class="col-lg-4">
            {{-- <b>Asset Name</b>: <span id="preview_details_name"></span><br><br> --}}
            <b>File Name</b>: <span id="preview_details_item_name"></span><br><br>
            <b>File Type</b>: <span id="preview_details_item_type"></span><br><br>
            <b>File Size</b>: <span id="preview_details_item_size"></span><br><br>
            <b>Uploaded</b>: <span id="preview_details_uploaded_at"></span><br><br>
            <span id="preview_details_dimensions"><b>Dimensions</b>: <span id="preview_details_dimensions_value"></span><br><br></span>
            <a href="#" download class="btn btn-primary" id="preview_details_url" target="_blank">Download Here</a>
        </div>
    </div>
@endsection
{{-- ADD HTML LARGE MODAL - END --}}

@extends('_template_adm.master')

@php
    use App\Libraries\Helper;

    $pagetitle = 'Dashboard';
@endphp

@section('title', $pagetitle)

@section('content')
    <div class="">
        {{-- display response message --}}
        @include('_template_adm.message')

        <div class="page-title">
            <div class="title_left">
                <h3>{{ $pagetitle }}</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        {{-- FILTER --}}
        <div class="row">
            {{-- filter by: period type --}}
            <div class="col-md-2 col-sm-12 col-xs-12">
                <div class="control-group">
                    <div class="controls">
                        <label>{{ ucwords(lang('period type', $translations)) }}</label>
                        <div class="input-prepend input-group">
                            <span class="add-on input-group-addon"><i class="fa fa-area-chart"></i></span>
                            <select id="filter_period" class="form-control">
                                <option value="daily" selected>{{ ucwords(lang('daily', $translations)) }}</option>
                                <option value="monthly">{{ ucwords(lang('monthly', $translations)) }}</option>
                                <option value="annual">{{ ucwords(lang('annual', $translations)) }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
                            
            {{-- filter by: daterange --}}
            <div class="col-md-4 col-sm-12 col-xs-12">
                <div class="control-group">
                    <div class="controls">
                        <label>{{ ucwords(lang('daterange', $translations)) }}</label>
                        <div class="input-prepend input-group">
                            <span class="add-on input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" id="filter_daterange" class="form-control" value="" autocomplete="off" />
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- action button(s) --}}
            <div class="col-md-2 col-sm-5 col-xs-12">
                {{-- action: search --}}
                <button class="btn btn-primary mt-3" style="margin-top: 24px;" id="btn-search">
                    <i class="fa fa-filter"></i>&nbsp; {{ ucwords(lang('filter', $translations)) }}
                </button>
            </div>
        </div>

        {{-- SUMMARY BOXES --}}
        <div class="row top_tiles row_box">
            <div class="animated flipInY col-lg-4 col-md-4 col-sm-6 col-xs-12 center_box">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-shopping-cart"></i></div>
                    <div class="count" id="top_tiles_1">120</div>
                    <h3>Total Customers</h3>
                    {{-- <p>Lorem ipsum psdea itgum rixt.</p> --}}
                </div>
            </div>
            <div class="animated flipInY col-lg-4 col-md-4 col-sm-6 col-xs-12 center_box">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-briefcase"></i></div>
                    <div class="count" id="top_tiles_2">10</div>
                    <h3>Total Suppliers</h3>
                    {{-- <p>Lorem ipsum psdea itgum rixt.</p> --}}
                </div>
            </div>
            <div class="animated flipInY col-lg-4 col-md-4 col-sm-6 col-xs-12 center_box">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-cubes"></i></div>
                    <div class="count" id="top_tiles_3">140</div>
                    <h3>Total Items</h3>
                    {{-- <p>Lorem ipsum psdea itgum rixt.</p> --}}
                </div>
            </div>
        </div>

        {{-- SUMMARY BOXES --}}
        <div class="row top_tiles row_box">
            <div class="animated flipInY col-lg-4 col-md-4 col-sm-6 col-xs-12 center_box">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-warning"></i></div>
                    <div class="count" id="top_tiles_4">15,000,000</div>
                    <h3>Customers' Unpaid Invoices</h3>
                    {{-- <p>Lorem ipsum psdea itgum rixt.</p> --}}
                </div>
            </div>
            <div class="animated flipInY col-lg-4 col-md-4 col-sm-6 col-xs-12 center_box">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-check-square-o"></i></div>
                    <div class="count" id="top_tiles_5">105,000,000</div>
                    <h3>Customers' Paid Invoices</h3>
                    {{-- <p>Lorem ipsum psdea itgum rixt.</p> --}}
                </div>
            </div>
            <div class="animated flipInY col-lg-4 col-md-4 col-sm-6 col-xs-12 center_box">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-minus-square"></i></div>
                    <div class="count" id="top_tiles_4">12,000,000</div>
                    <h3>Suppliers's Unpaid Invoices</h3>
                    {{-- <p>Lorem ipsum psdea itgum rixt.</p> --}}
                </div>
            </div>
        </div>

        {{-- LINE CHARTS --}}
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Sales</h2>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        <div id="echarts_sales" style="height:350px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <!-- bootstrap-daterangepicker -->
    @include('_vendors.daterangepicker.css')

    <style>
        /* .row_box {
            text-align: center;
        }
        .center_box {
            float: none !important;
            display: inline-block;
            text-align: left;
        } */
    </style>
@endsection

@section('script')
    <!-- bootstrap-daterangepicker -->
    @include('_vendors.daterangepicker.script')
    <script>
        var filter_daterange = '';
        var filter_period = '';

        $(document).ready(function() {
            init_daterangepicker_custom('filter_daterange');

            refresh_data();

            $('#btn-search').on('click', function() {
                refresh_data()
                $(this).blur();
            });
        });
    </script>

    <!--  ECharts -->
    <script src="{{ asset('vendors/echarts/dist/echarts.min.js') }}"></script>
    <script>
        var echarts_theme = {
            color: [
                '#337ab7', '#5cb85c', '#777', '#f0ad4e'
            ],

            title: {
                itemGap: 8,
                textStyle: {
                    fontWeight: 'normal',
                    color: '#408829'
                }
            },

            dataRange: {
                color: ['#1f610a', '#97b58d']
            },

            toolbox: {
                color: ['#408829', '#408829', '#408829', '#408829']
            },

            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.5)',
                axisPointer: {
                    type: 'line',
                    lineStyle: {
                        color: '#408829',
                        type: 'dashed'
                    },
                    crossStyle: {
                        color: '#408829'
                    },
                    shadowStyle: {
                        color: 'rgba(200,200,200,0.3)'
                    }
                }
            },

            dataZoom: {
                dataBackgroundColor: '#eee',
                fillerColor: 'rgba(64,136,41,0.2)',
                handleColor: '#408829'
            },
            grid: {
                borderWidth: 0
            },

            categoryAxis: {
                axisLine: {
                    lineStyle: {
                        color: '#408829'
                    }
                },
                splitLine: {
                    lineStyle: {
                        color: ['#eee']
                    }
                }
            },

            valueAxis: {
                axisLine: {
                    lineStyle: {
                        color: '#408829'
                    }
                },
                splitArea: {
                    show: true,
                    areaStyle: {
                        color: ['rgba(250,250,250,0.1)', 'rgba(200,200,200,0.1)']
                    }
                },
                splitLine: {
                    lineStyle: {
                        color: ['#eee']
                    }
                }
            },
            timeline: {
                lineStyle: {
                    color: '#408829'
                },
                controlStyle: {
                    normal: {
                        color: '#408829'
                    },
                    emphasis: {
                        color: '#408829'
                    }
                }
            },

            k: {
                itemStyle: {
                    normal: {
                        color: '#68a54a',
                        color0: '#a9cba2',
                        lineStyle: {
                            width: 1,
                            color: '#408829',
                            color0: '#86b379'
                        }
                    }
                }
            },
            map: {
                itemStyle: {
                    normal: {
                        areaStyle: {
                            color: '#ddd'
                        },
                        label: {
                            textStyle: {
                                color: '#c12e34'
                            }
                        }
                    },
                    emphasis: {
                        areaStyle: {
                            color: '#99d2dd'
                        },
                        label: {
                            textStyle: {
                                color: '#c12e34'
                            }
                        }
                    }
                }
            },
            force: {
                itemStyle: {
                    normal: {
                        linkStyle: {
                            strokeColor: '#408829'
                        }
                    }
                }
            },
            chord: {
                padding: 4,
                itemStyle: {
                    normal: {
                        lineStyle: {
                            width: 1,
                            color: 'rgba(128, 128, 128, 0.5)'
                        },
                        chordStyle: {
                            lineStyle: {
                                width: 1,
                                color: 'rgba(128, 128, 128, 0.5)'
                            }
                        }
                    },
                    emphasis: {
                        lineStyle: {
                            width: 1,
                            color: 'rgba(128, 128, 128, 0.5)'
                        },
                        chordStyle: {
                            lineStyle: {
                                width: 1,
                                color: 'rgba(128, 128, 128, 0.5)'
                            }
                        }
                    }
                }
            },
            gauge: {
                startAngle: 225,
                endAngle: -45,
                axisLine: {
                    show: true,
                    lineStyle: {
                        color: [
                            [0.2, '#86b379'],
                            [0.8, '#68a54a'],
                            [1, '#408829']
                        ],
                        width: 8
                    }
                },
                axisTick: {
                    splitNumber: 10,
                    length: 12,
                    lineStyle: {
                        color: 'auto'
                    }
                },
                axisLabel: {
                    textStyle: {
                        color: 'auto'
                    }
                },
                splitLine: {
                    length: 18,
                    lineStyle: {
                        color: 'auto'
                    }
                },
                pointer: {
                    length: '90%',
                    color: 'auto'
                },
                title: {
                    textStyle: {
                        color: '#333'
                    }
                },
                detail: {
                    textStyle: {
                        color: 'auto'
                    }
                }
            },
            textStyle: {
                fontFamily: 'Arial, Verdana, sans-serif'
            }
        };
        
        function init_echarts_sales() {
            if (typeof(echarts) === 'undefined') {
                return;
            }
            console.log('init_echarts_sales');

            if ($('#echarts_sales').length) {
                var echartSales = echarts.init(
                    document.getElementById('echarts_sales'), 
                    echarts_theme
                );

                function generateRedeemChart(data) {
                    var legends = data.legends;
                    var periods = data.periods;
                    var series = data.series;

                    echartSales.setOption({
                        tooltip: {
                            trigger: 'axis'
                        },
                        legend: {
                            x: 220,
                            y: 40,
                            data: legends
                        },
                        toolbox: {
                            show: true,
                            feature: {
                                magicType: {
                                    show: true,
                                    title: {
                                        line: 'Line',
                                        bar: 'Bar'
                                    },
                                    type: [
                                     'line',
                                     'bar'
                                    ]
                                },
                                restore: {
                                    show: true,
                                    title: "Restore"
                                },
                                saveAsImage: {
                                    show: true,
                                    title: "Save Image"
                                }
                            }
                        },
                        calculable: true,
                        xAxis: [{
                            type: 'category',
                            boundaryGap: false,
                            data: periods
                        }],
                        yAxis: [{
                            type: 'value'
                        }],
                        series: series
                    });
                }
                
                // Make an AJAX request to fetch chart data
                $.ajax({
                    url: "{{ route('admin.dashboard.dummy_data') }}?daterange="+filter_daterange+"&period="+filter_period,
                    method: 'GET',
                    success: function(response) {
                        generateRedeemChart(response); // Call the function to update the chart
                    },
                    error: function(error) {
                        console.error("Error fetching chart data:", error);
                    }
                });
            }
        }
    </script>

    <script>
        function refresh_data() {
            filter_daterange = $('#filter_daterange').val();
            if (typeof filter_daterange == 'undefined' || filter_daterange === null) {
                filter_daterange = '';
            }
            set_param_url('daterange', filter_daterange);

            filter_period = $('#filter_period').val();
            if (typeof filter_period == 'undefined' || filter_period === null) {
                filter_period = '';
            }
            set_param_url('period', filter_period);

            init_echarts_sales();
        }
    </script>
@endsection