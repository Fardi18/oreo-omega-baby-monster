@extends('_template_adm.master')

@php
    $pagetitle = ucwords(lang('tester form', $translations));
    $link = route('dev.tester_form');
    $data = null;
@endphp

@section('title', $pagetitle)

@section('content')
    <div class="">
        <!-- message info -->
        @include('_template_adm.message')

        <div class="page-title">
            <div class="title_left">
                <h3>{{ $pagetitle }}</h3>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>{{ ucwords(lang('form details', $translations)) }}</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <canvas id="signatureCanvas" width="400" height="200"></canvas>
                        <br />
                        <button onclick="clearSignature()">Clear Signature</button>
                        <button onclick="generateImage()">Generate Image</button>
                        <button onclick="convertToFile()">Download File</button>
                        <br />
                        <img id="generatedImage" alt="Generated Signature" />

                        <form id="form_data" class="form-horizontal form-label-left" action="{{ $link }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="esignature" id="esignature">
                            
                            <div class="ln_solid"></div>

                            <div class="form-group">
                                @php
                                    echo set_input_form('switch', 'stay_on_page', ucfirst(lang('stay on this page after submitting', $translations)), $data, $errors, false);
                                @endphp
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i>&nbsp; 
                                        @if (isset($data))
                                            {{ ucwords(lang('save', $translations)) }}
                                        @else
                                            {{ ucwords(lang('submit', $translations)) }}
                                        @endif
                                    </button>
                                    <a href="{{ route('admin.home') }}" class="btn btn-default"><i class="fa fa-times"></i>&nbsp; 
                                        @if (isset($data))
                                            {{ ucwords(lang('close', $translations)) }}
                                        @else
                                            {{ ucwords(lang('cancel', $translations)) }}
                                        @endif
                                    </a>

                                    @if (isset($raw_id))
                                        | <span class="btn btn-danger" onclick="$('#form_delete').submit()"><i class="fa fa-trash"></i></span>
                                    @endif
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <!-- Switchery -->
    @include('_vendors.switchery.css')
    <style>
      #signatureCanvas {
        border: 1px solid #ccc;
      }
    </style>
@endsection

@section('script')
    <!-- Switchery -->
    @include('_vendors.switchery.script')

    
    <script>
        var ctx, canvas;
        $(document).ready(function() {
            canvas = document.getElementById('signatureCanvas');
            console.log(canvas, 'canvas')
            ctx = canvas.getContext('2d');
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = 'black';
    
            let isDrawing = false;
    
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);
    
            function startDrawing(e) {
                isDrawing = true;
                draw(e);
            }
    
            function draw(e) {
                if (!isDrawing) return;
                ctx.lineTo(e.offsetX, e.offsetY);
                ctx.stroke();
                ctx.beginPath();
                ctx.moveTo(e.offsetX, e.offsetY);
                console.log(e, 'event')
            }
    
            function stopDrawing() {
                isDrawing = false;
                ctx.beginPath();
            }
    
     
        })
        function clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
        function generateImage() {
            const signatureImage = new Image();
            signatureImage.src = canvas.toDataURL();
            document.getElementById('generatedImage').src = signatureImage.src;
            document.getElementById('esignature').value = signatureImage.src;
        }
        function convertToFile() {
            const signatureImage = canvas.toDataURL('image/png');
            const file = dataURLtoFile(signatureImage, 'signature.png');

            // Create a download link
            const downloadLink = document.createElement('a');
            downloadLink.href = URL.createObjectURL(file);
            downloadLink.download = file.name;

            // Trigger the download
            downloadLink.click();

            // Cleanup
            URL.revokeObjectURL(downloadLink.href);
        }

        // Helper function to convert dataURL to File
        function dataURLtoFile(dataURL, filename) {
            const arr = dataURL.split(',');
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);
            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }
            return new File([u8arr], filename, {type: mime});
        }
    </script>
@endsection