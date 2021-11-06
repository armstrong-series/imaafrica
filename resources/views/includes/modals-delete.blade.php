<!-- Delete Modal -->
    <!-- Modal -->
    <div class="modal fade" id="deleteTrack" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <img src="{{ asset('img-category/error1.png') }}" class="mx-auto d-block" width="90" height="113">
                    <h5 class="text-center">This data will be lost! Are you sure?</h5>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="button" @click='deletePlaylist()' class="btn btn-danger btn-sm">Confirm</button>
                </div>
            </div>
        </div>
    </div>
<!-- End Delete -->