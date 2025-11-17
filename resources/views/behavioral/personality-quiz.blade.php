@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Financial Personality Assessment</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Discover Your Financial Personality</h6>
                </div>
                <div class="card-body">
                    <form id="personality-quiz" method="POST" action="{{ route('behavioral.personality.store') }}">
                        @csrf

                        <!-- Question 1 -->
                        <div class="question mb-4">
                            <h5>1. When you receive unexpected money (bonus, gift), what do you usually do?</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q1" value="save" id="q1a" required>
                                <label class="form-check-label" for="q1a">
                                    Save it for future needs or investments
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q1" value="spend" id="q1b">
                                <label class="form-check-label" for="q1b">
                                    Spend it on something fun or needed
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q1" value="invest" id="q1c">
                                <label class="form-check-label" for="q1c">
                                    Invest it in stocks, crypto, or other assets
                                </label>
                            </div>
                        </div>

                        <!-- Question 2 -->
                        <div class="question mb-4">
                            <h5>2. How do you feel about debt?</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q2" value="avoid" id="q2a" required>
                                <label class="form-check-label" for="q2a">
                                    I avoid debt at all costs
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q2" value="strategic" id="q2b">
                                <label class="form-check-label" for="q2b">
                                    I use debt strategically (mortgage, education)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q2" value="comfortable" id="q2c">
                                <label class="form-check-label" for="q2c">
                                    I'm comfortable with some debt for lifestyle
                                </label>
                            </div>
                        </div>

                        <!-- Question 3 -->
                        <div class="question mb-4">
                            <h5>3. What's your approach to budgeting?</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q3" value="strict" id="q3a" required>
                                <label class="form-check-label" for="q3a">
                                    I track every expense meticulously
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q3" value="flexible" id="q3b">
                                <label class="form-check-label" for="q3b">
                                    I have rough guidelines but stay flexible
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q3" value="minimal" id="q3c">
                                <label class="form-check-label" for="q3c">
                                    I don't budget much, I just spend what I need
                                </label>
                            </div>
                        </div>

                        <!-- Question 4 -->
                        <div class="question mb-4">
                            <h5>4. How do you react to sales or discounts?</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q4" value="ignore" id="q4a" required>
                                <label class="form-check-label" for="q4a">
                                    I buy only what I need, regardless of price
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q4" value="smart" id="q4b">
                                <label class="form-check-label" for="q4b">
                                    I look for good deals but don't overspend
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q4" value="enthusiastic" id="q4c">
                                <label class="form-check-label" for="q4c">
                                    I love sales and often buy things I don't need
                                </label>
                            </div>
                        </div>

                        <!-- Question 5 -->
                        <div class="question mb-4">
                            <h5>5. What's your investment risk tolerance?</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q5" value="low" id="q5a" required>
                                <label class="form-check-label" for="q5a">
                                    I prefer safe, guaranteed returns
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q5" value="moderate" id="q5b">
                                <label class="form-check-label" for="q5b">
                                    I'm willing to take some risk for better returns
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q5" value="high" id="q5c">
                                <label class="form-check-label" for="q5c">
                                    I'm comfortable with high risk for high rewards
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg">Get My Financial Personality</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">What You'll Learn</h6>
                </div>
                <div class="card-body">
                    <p>This assessment will help you understand:</p>
                    <ul>
                        <li>Your natural spending tendencies</li>
                        <li>Risk tolerance for investments</li>
                        <li>Approach to saving and budgeting</li>
                        <li>Personalized financial recommendations</li>
                    </ul>

                    <div class="alert alert-info">
                        <strong>Note:</strong> This is for educational purposes. Financial decisions should consider your full situation.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
