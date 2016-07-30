<div class="modal" id="delete-item-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title"> 
                    Delete
                    <span class="item-type capitalize"></span>
                </h4>
            </div>
            <div class="modal-body">
                <p>
                    Are you sure you want to delete <span class="item-name"></span>?
                    This cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                {!! Form::open(['id' => 'delete-item-form']) !!}
                    {{ method_field('DELETE') }}
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Delete
                    </button>
                {!! Form::close() !!}
            </div>
        </div>
    <!-- /.modal-content -->
    </div>
<!-- /.modal-dialog -->
</div>
