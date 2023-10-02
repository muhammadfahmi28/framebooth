<!-- Button trigger modal -->
<a class="app-btn-logout load-hide opacity-0" data-bs-toggle="modal" data-bs-target="#logoutModal">
&nbsp;
</a>

{{-- <a class="app-btn-logout load-hide opacity-0" href="/logout"> --}}
&nbsp;
</a>

<!-- Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered app-modal">
        <div class="modal-content">
        <div class="modal-header">
            {{-- <h1 class="modal-title fs-5" id="logoutModalLabel">Modal title</h1> --}}
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            Logout?
        </div>
        <div class="modal-footer">
            <a href="#" data-href="{{url('/logout')}}" class="btn btn-danger app-modal-link" data-bs-dismiss="modal" data-modal="#logoutModal">Logout</a>
        </div>
        </div>
    </div>
</div>
