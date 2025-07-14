@extends('_template_web.master')

@php
    $pagetitle = 'Pre-Launch Registration';
    $locale = app()->getLocale();
@endphp

@section('title', $pagetitle)

@section('style')
    <style>
        .landing-page {
            max-width: 400px;
            margin: 0 auto;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .landing-page h2 {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .landing-page p {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }

        .landing-page input[type="text"],
        .landing-page input[type="email"],
        .landing-page select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            color: #000;
            background-color: #fff;
        }

        .phone-group {
            display: flex;
            gap: 10px;
        }

        .name-group {
            display: flex;
            gap: 10px;
        }

        .dob-group {
            display: flex;
            gap: 10px;
        }

        .landing-page button {
            background-color: black;
            color: white;
            padding: 10px 0;
            border: none;
            width: 100%;
            border-radius: 20px;
            margin: 10px 0;
            font-size: 16px;
            cursor: pointer;
            position: relative;
        }

        .landing-page button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .checkbox-group {
            text-align: left;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .checkbox-group label {
            display: block;
            margin: 5px 0;
        }

        .social-section {
            font-size: 14px;
        }

        .social-icons img {
            width: 40px;
            margin: 5px;
        }

        .footer-links {
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')
    <section>
        <div class="landing-page">
            <h2>Oreo Baby Monster</h2>
            <p>{{ __('landing_page.subtitle-1') }}<br>{{ __('landing_page.subtitle-2') }}</p>

            @if ($errors->any())
                <script>
                    alert(`{!! implode('\n', $errors->all()) !!}`);
                </script>
            @endif

            @if (session('success'))
                <script>
                    alert(`{{ session('success') }}`);
                </script>
            @endif

            <form
                action="{{ route('web.register_post_launch_process', ['market' => request()->segment(1), 'lang' => request()->segment(2)]) }}"
                method="POST">
                @csrf
                <div class="phone-group">
                    @php
                        $currentMarketAlias = strtoupper(request()->segment(1));
                        $selectedMarket = $markets->firstWhere('country_alias', $currentMarketAlias);
                        $selectedMarketId = old('market_id') ?? ($selectedMarket->id ?? '');
                    @endphp

                    <select id="countryCode" style="flex: 1;" name="market_id" required>
                        <option value="">Choose One</option>
                        @foreach ($markets as $market)
                            @php
                                $alias = strtolower($market->country_alias);
                                $defaultLang = config('market')[$alias][0] ?? 'en';
                            @endphp
                            <option value="{{ $market->id }}" data-alias="{{ $alias }}"
                                data-default-lang="{{ $defaultLang }}"
                                {{ $selectedMarketId == $market->id ? 'selected' : '' }}>
                                +{{ $market->country_code }}
                            </option>
                        @endforeach
                    </select>

                    <input type="tel" name="phone_number" id="phone_number" placeholder="9232 2232" style="flex: 2;"
                        value="{{ old('phone_number') }}">
                </div>

                <div class="name-group">
                    <input type="text" name="first_name" id="first_name" placeholder="Your First Name" required
                        value="{{ old('first_name') }}">
                    <input type="text" name="last_name" id="last_name" placeholder="Your Last Name" required
                        value="{{ old('last_name') }}">
                </div>

                <div>
                    <input type="email" placeholder="Your Email" name="email" id="emailField" required
                        value="{{ old('email') }}">
                </div>

                <div class="dob-group">
                    <select id="dob-year" name="dob_year" required>
                        <option value="">Year</option>
                    </select>
                    <select id="dob-month" name="dob_month" required>
                        <option value="">Month</option>
                    </select>
                    <select id="dob-day" name="dob_day" required>
                        <option value="">Date</option>
                    </select>
                </div>

                <button type="submit" id="submit-button">
                    Submit
                </button>
            </form>

            <div class="checkbox-group">
                <label><input type="checkbox" id="terms" required> Term & Condition</label>
                <label><input type="checkbox" id="privacy" required> Privacy Polish</label>
                <label><input type="checkbox" id="marketing" required> Marketing Communication</label>
            </div>

            <div class="social-section">
                or Follow our <strong>social media</strong> Channels to get an update when the activity run
                <div class="social-icons">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png" alt="Oreo IG">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg"
                        alt="Oreo FB">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b8/YouTube_Logo_2017.svg" alt="Oreo YouTube">
                </div>
            </div>

            <div class="footer-links">
                Terms & Condition | Privacy Polish | Cookie Policy | Contact Us
            </div>
        </div>
    </section>
@endsection

@section('footer-script')
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.4.3/build/global/luxon.min.js"></script>

    <script>
        // hide the email field if the selected market alias is 'kh'
        function toggleEmailField() {
            const marketSelect = document.getElementById('countryCode');
            const emailInput = document.getElementById('emailField');
            const selectedOption = marketSelect.options[marketSelect.selectedIndex];
            const alias = selectedOption.dataset.alias;

            if (alias === 'kh') {
                emailInput.closest('div').style.display = 'none';
                emailInput.disabled = true;
            } else {
                emailInput.closest('div').style.display = '';
                emailInput.disabled = false;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const marketSelect = document.getElementById('countryCode');

            if (marketSelect) {
                // Jalankan saat awal
                toggleEmailField();

                // Jalankan setiap kali dropdown berubah
                marketSelect.addEventListener('change', function() {
                    toggleEmailField();
                });
            }
        });

        // Handle market selection change to submit the form
        document.addEventListener('DOMContentLoaded', function() {
            const marketSelect = document.getElementById('countryCode');

            if (marketSelect) {
                marketSelect.addEventListener('change', function() {
                    const selectedMarketId = this.value;

                    // Jika belum memilih (value kosong), hentikan
                    if (!selectedMarketId) {
                        return;
                    }

                    const form = this.closest('form');

                    // Create a temporary form to submit
                    const tempForm = document.createElement('form');
                    tempForm.method = 'POST';
                    tempForm.action = '{{ route('language.switch') }}';

                    // Set the market_id and lang from the selected option
                    const csrf = document.querySelector('input[name="_token"]').cloneNode();
                    tempForm.appendChild(csrf);

                    new FormData(form).forEach((value, name) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name;
                        input.value = value;
                        tempForm.appendChild(input);
                    });

                    document.body.appendChild(tempForm);
                    tempForm.submit();
                });

            }
        });

        // Luxon for date handling
        const DateTime = luxon.DateTime;

        const yearSelect = document.getElementById("dob-year");
        const monthSelect = document.getElementById("dob-month");
        const daySelect = document.getElementById("dob-day");

        // Get old values first
        const oldYear = '{{ old('dob_year') }}';
        const oldMonth = '{{ old('dob_month') }}';
        const oldDay = '{{ old('dob_day') }}';

        // Generate years first (descending)
        const currentYear = DateTime.now().year;
        for (let y = currentYear; y >= 1900; y--) {
            const opt = document.createElement("option");
            opt.value = y;
            opt.text = y;
            yearSelect.appendChild(opt);
        }

        // Generate months
        const monthNames = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        monthNames.forEach((month, index) => {
            const opt = document.createElement("option");
            opt.value = index + 1; // month is 1-indexed in Luxon
            opt.text = month;
            monthSelect.appendChild(opt);
        });

        // Recalculate days when year or month changes
        function populateDays(year, month, selectedDay = null) {
            daySelect.innerHTML = '<option value="">Date</option>'; // reset
            if (!year || !month) return;

            const daysInMonth = DateTime.local(parseInt(year), parseInt(month)).daysInMonth;
            for (let d = 1; d <= daysInMonth; d++) {
                const opt = document.createElement("option");
                opt.value = d;
                opt.text = d;
                daySelect.appendChild(opt);
            }

            // Set the selected day if provided and valid
            if (selectedDay && selectedDay <= daysInMonth) {
                daySelect.value = selectedDay;
            }
        }

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', () => {
            // Set old values if they exist
            if (oldYear) yearSelect.value = oldYear;
            if (oldMonth) monthSelect.value = oldMonth;

            // Populate days if we have both year and month
            if (yearSelect.value && monthSelect.value) {
                populateDays(yearSelect.value, monthSelect.value, oldDay);
            }
        });

        // Handle alert closing
        if (document.querySelector('.alert')) {
            document.querySelector('.alert').addEventListener('closed.bs.alert', () => {
                // Repopulate days after alert is closed if we have year and month
                if (yearSelect.value && monthSelect.value) {
                    populateDays(yearSelect.value, monthSelect.value, daySelect.value);
                }
            });
        }

        // Listen to changes
        yearSelect.addEventListener("change", () => {
            if (monthSelect.value) {
                populateDays(yearSelect.value, monthSelect.value, daySelect.value);
            }
        });

        monthSelect.addEventListener("change", () => {
            if (yearSelect.value) {
                populateDays(yearSelect.value, monthSelect.value, daySelect.value);
            }
        });

        // Also handle the alert dialog closing
        if (typeof alert !== 'undefined') {
            const originalAlert = window.alert;
            window.alert = function(message) {
                originalAlert(message);
                // After alert is closed, repopulate days if needed
                if (yearSelect.value && monthSelect.value) {
                    populateDays(yearSelect.value, monthSelect.value, oldDay);
                }
            };
        }

        // Handle checkbox validation and submit button state
        document.addEventListener('DOMContentLoaded', function() {
            const submitButton = document.getElementById('submit-button');
            const checkboxes = ['terms', 'privacy', 'marketing'].map(id => document.getElementById(id));

            // Disable submit button by default
            submitButton.disabled = true;

            function validateCheckboxes() {
                const allChecked = checkboxes.every(checkbox => checkbox.checked);
                submitButton.disabled = !allChecked;
            }

            // Add event listeners to all checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', validateCheckboxes);
            });
        });
    </script>
@endsection
