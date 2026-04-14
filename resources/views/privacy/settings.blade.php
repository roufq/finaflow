@extends('layouts.app')

@section('content')
<div class="container-fluid">

 <!-- Page Heading -->
 <div class="d-sm-flex align-items-center justify-content-between mb-4">
  <h1 class="h3 mb-0 text-gray-800">{{ __('privacy.title') }}</h1>
  <a href="{{ route('dashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
   <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ __('forms.labels.back') }} {{ __('navigation.dashboard') }}
  </a>
 </div>

 @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
   <i class="fas fa-check-circle"></i> {{ session('success') }}
   <button type="button" class="close" data-dismiss="alert" aria-tags="Close">
    <span aria-hidden="true">&times;</span>
   </button>
  </div>
 @endif

 <div class="row">
  <div class="col-lg-8">
   <div class="card shadow mb-4">
    <div class="card-header py-3">
     <h6 class="m-0 font-weight-bold text-primary">{{ __('privacy.data_privacy_preferences') }}</h6>
    </div>
    <div class="card-body">
     <form method="POST" action="{{ route('privacy.update') }}">
      @csrf
      @method('PUT')

      <div class="form-group">
       <div class="custom-control custom-switch">
        <input type="hidden" name="data_analytics" value="0">
        <input type="checkbox" class="custom-control-input" id="data_analytics"
         name="data_analytics" value="1" {{ $privacySettings->data_analytics ? 'checked' : '' }}>
        <tags class="custom-control-tags" for="data_analytics">
         <strong>{{ __('privacy.data_analytics') }}</strong>
        </tags>
       </div>
       <small class="form-text text-muted">
        {{ __('privacy.data_analytics_description') }}
       </small>
      </div>

      <div class="form-group">
       <div class="custom-control custom-switch">
        <input type="hidden" name="behavioral_insights" value="0">
        <input type="checkbox" class="custom-control-input" id="behavioral_insights"
         name="behavioral_insights" value="1" {{ $privacySettings->behavioral_insights ? 'checked' : '' }}>
        <tags class="custom-control-tags" for="behavioral_insights">
         <strong>{{ __('privacy.behavioral_insights') }}</strong>
        </tags>
       </div>
       <small class="form-text text-muted">
        {{ __('privacy.behavioral_insights_description') }}
       </small>
      </div>

      <div class="form-group">
       <div class="custom-control custom-switch">
        <input type="hidden" name="third_party_sharing" value="0">
        <input type="checkbox" class="custom-control-input" id="third_party_sharing"
         name="third_party_sharing" value="1" {{ $privacySettings->third_party_sharing ? 'checked' : '' }}>
        <tags class="custom-control-tags" for="third_party_sharing">
         <strong>{{ __('privacy.third_party_sharing') }}</strong>
        </tags>
       </div>
       <small class="form-text text-muted">
        {{ __('privacy.third_party_sharing_description') }}
       </small>
      </div>

      <div class="form-group">
       <div class="custom-control custom-switch">
        <input type="hidden" name="data_anonymization" value="0">
        <input type="checkbox" class="custom-control-input" id="data_anonymization"
         name="data_anonymization" value="1" {{ $privacySettings->data_anonymization ? 'checked' : '' }}>
        <tags class="custom-control-tags" for="data_anonymization">
         <strong>{{ __('privacy.data_anonymization') }}</strong>
        </tags>
       </div>
       <small class="form-text text-muted">
        {{ __('privacy.data_anonymization_description') }}
       </small>
      </div>

      <button type="submit" class="btn btn-primary">
       <i class="fas fa-save"></i> {{ __('privacy.save_privacy_settings') }}
      </button>
     </form>
    </div>
   </div>

   <!-- Account Management -->
   <div class="card shadow mb-4">
    <div class="card-header py-3">
     <h6 class="m-0 font-weight-bold text-warning">{{ __('privacy.account_management') }}</h6>
    </div>
    <div class="card-body">
     <div class="row">
      <div class="col-md-6">
       <h6>{{ __('privacy.data_export') }}</h6>
       <p class="text-muted">{{ __('privacy.data_export_description') }}</p>
       <a href="{{ route('privacy.export-data') }}" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-download"></i> {{ __('privacy.export_my_data') }}
       </a>
      </div>
      <div class="col-md-6">
       <h6>{{ __('privacy.account_deletion') }}</h6>
       @if($privacySettings->account_deletion)
        <div class="alert alert-danger">
         <strong>{{ __('privacy.deletion_requested') }}</strong><br>
         {{ __('privacy.deletion_pending_message') }}
        </div>
        <form method="POST" action="{{ route('privacy.cancel-deletion') }}" class="d-inline">
         @csrf
         <button type="submit" class="btn btn-outline-secondary btn-sm">
          <i class="fas fa-undo"></i> {{ __('privacy.cancel_deletion_request') }}
         </button>
        </form>
       @else
        <p class="text-muted">{{ __('privacy.account_deletion_description') }}</p>
        <button type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#deleteModal">
         <i class="fas fa-trash"></i> {{ __('privacy.request_account_deletion') }}
        </button>
       @endif
      </div>
     </div>
    </div>
   </div>
  </div>

  <div class="col-lg-4">
   <div class="card shadow mb-4">
    <div class="card-header py-3">
     <h6 class="m-0 font-weight-bold text-info">{{ __('privacy.privacy_information') }}</h6>
    </div>
    <div class="card-body">
     <h6>{{ __('privacy.what_we_collect') }}</h6>
     <ul class="mb-3">
      <li>{{ __('privacy.financial_transaction_data') }}</li>
      <li>{{ __('privacy.account_budget_info') }}</li>
      <li>{{ __('privacy.behavioral_insights') }}</li>
      <li>{{ __('privacy.usage_analytics') }}</li>
     </ul>

     <h6>{{ __('privacy.how_we_use_data') }}</h6>
     <ul class="mb-3">
      <li>{{ __('privacy.provide_services') }}</li>
      <li>{{ __('privacy.generate_insights') }}</li>
      <li>{{ __('privacy.improve_services') }}</li>
      <li>{{ __('privacy.ensure_security') }}</li>
     </ul>

     <h6>{{ __('privacy.your_rights') }}</h6>
     <ul class="mb-3">
      <li>{{ __('privacy.access_data') }}</li>
      <li>{{ __('privacy.correct_data') }}</li>
      <li>{{ __('privacy.delete_account') }}</li>
      <li>{{ __('privacy.opt_out_sharing') }}</li>
     </ul>

     <div class="alert alert-info">
      <i class="fas fa-shield-alt"></i> <strong>{{ __('privacy.security') }}:</strong> {{ __('privacy.security_note') }}
     </div>
    </div>
   </div>

   <!-- Privacy Policy Link -->
   <div class="card shadow mb-4">
    <div class="card-body text-center">
     <h6>{{ __('privacy.need_more_info') }}</h6>
     <a href="#" class="btn btn-link">{{ __('privacy.view_full_privacy_policy') }}</a><br>
     <a href="#" class="btn btn-link">{{ __('privacy.contact_data_protection_officer') }}</a>
    </div>
   </div>
  </div>
 </div>

</div>

<!-- Account Deletion Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
 <div class="modal-dialog" role="document">
  <div class="modal-content">
   <div class="modal-header">
    <h5 class="modal-title" id="deleteModalLabel">{{ __('privacy.confirm_account_deletion') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-tags="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <div class="modal-body">
    <div class="alert alert-danger">
     <strong>{{ __('privacy.warning') }}:</strong> {{ __('privacy.deletion_warning') }}
    </div>
    <p>{{ __('privacy.before_proceeding') }}</p>
    <ul>
     <li>{{ __('privacy.export_data_first') }}</li>
     <li>{{ __('privacy.family_data_deleted') }}</li>
     <li>{{ __('privacy.subscription_info_removed') }}</li>
     <li>{{ __('privacy.lose_premium_access') }}</li>
    </ul>
    <p>{{ __('privacy.sure_delete') }}</p>
   </div>
   <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('forms.labels.cancel') }}</button>
    <form method="POST" action="{{ route('privacy.request-deletion') }}" class="d-inline">
     @csrf
     <button type="submit" class="btn btn-danger">
      <i class="fas fa-trash"></i> {{ __('privacy.yes_delete_account') }}
     </button>
    </form>
   </div>
  </div>
 </div>
</div>

@endsection

