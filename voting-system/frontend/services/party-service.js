var PartyService={
    init: function(){
        $("#add-party-form").validate({
            rules:{
                name: 'required',
                abbreviation: {
                    required: true,
                    maxlength: 3,
                    minlength: 3
                }
            },
            messages:{
                name: 'Please enter a valid party name',
                abbreviation:{
                    required: 'Please enter an abbreviation',
                    maxlength: 'The abbreviation should be exactly 3 letters long',
                    minlength: 'The abbreviation should be exactly 3 letters long'
                }   
            },
            submitHandler: function(form){
                let party = Object.fromEntries(new FormData(form).entries());
                PartyService.addParty(party);
                form.reset();
            }
        });
        $("#edit-party-form").validate({
            rules:{
                name: 'required',
                abbreviation: {
                    required: true,
                    maxlength: 3
                }
            },
            messages:{
                name: 'Please enter a valid party name',
                abbreviation:{
                    required: 'Please enter an abbreviation',
                    maxlength: 'The abbreviation should be exactly 3 letters long',
                }   
            },
            submitHandler: function(form){
                let party = Object.fromEntries(new FormData(form).entries());
                PartyService.editParty(party);
            }
        });
        PartyService.generatePartiesDatatable();
    },
    generatePartiesDatatable: function(){
        const token = localStorage.getItem("user_token");
        if(token){
            const user = Utils.parseJwt(token).user;
            if(user && user.role){
            if(user.role === Constants.ADMIN_ROLE){
                RestClient.get('parties', function(data){
                $("#admin-parties-section").empty();
                $("#admin-parties-section").append(`<section class="py-5 border-top" id="registered-parties"></section>`);
                $("#registered-parties").empty();
                $("#registered-parties").append(`
                    <div class="container px-5 my-5">
                    <div class="text-center mb-5">
                        <h2 class="fw-bolder">Currently Registered Parties</h2>
                        <p class="lead mb-0 text-muted">Below is a list of all parties currently registered in the system</p>
                    </div>
                    <div id="admin-add-party-button-div">
                    
                    </div>
                    <div class="table-responsive">
                    <table id="partiesTable" class="table table-dark table-bordered text-center">
                    </table>
                    </div>
                    </div>
                
                `);
                $("#admin-add-party-button-div").empty();
                $("#admin-add-party-button-div").append(`<button type="button" class="btn btn-danger btn-sm" id="admin-add-party-button" data-bs-toggle="modal" data-bs-target="#examplePartyModal4" style="margin-left: 1.5vh;">Add party</button>`);
                Utils.datatable("partiesTable", [
                    {data: 'name', title: 'Name'},
                    {data: 'abbreviation', title: 'Abbreviation'},
                    {title: 'Actions',
                        render: function(data, type, row, meta){
                                    const rowStr = encodeURIComponent(JSON.stringify(row));
                                    return `<div class="d-flex flex-wrap justify-content-center gap-2">
                                            <button type="button" class="btn btn-success btn-sm" id="admin-view-party-button" data-bs-toggle="modal" data-bs-target="#examplePartyModal" onclick="PartyService.populatePartyViewMoreModal('${row.id}')">View</button>
                                            <button type="button" class="btn btn-info btn-sm" id="admin-edit-party-button" data-bs-toggle="modal" data-bs-target="#examplePartyModal2" onclick="PartyService.openPartyEditModal('${row.id}')">Edit</button>
                                            <button type="button" class="btn btn-danger btn-sm" id="admin-delete-party-button" data-bs-toggle="modal" data-bs-target="#examplePartyModal3" onclick="PartyService.openPartyConfirmationDialog(decodeURIComponent('${rowStr}'))">Delete</button>
                                        </div>`;
                        }
                    }
                ], data, 10 );

                }, function(xhr, status, error){
                console.error(error);
                });
            }
            }
      }else{
        return window.location.replace("index.html");
      }
    },
    getPartyNameById: function(id, callback){
        RestClient.get("party_by_id?id="+id, function(party){
            callback(party);
        }, function(xhr, status, error){
            console.error("Error fetching party by id: ", error);
        });
    },
    populatePartyViewMoreModal: function(id){
        $("#admin-row-parties-view-more").empty();
        RestClient.get(`party/${id}`, function(party){
            $("#admin-row-parties-view-more").append(`
                <tr>
                    <td>${party.name}</td>
                    <td>${party.abbreviation}</td>
                </tr>
            `);
        }, function(xhr, status, error){
            console.error("Error fetching party by id: ", error);
        });
    },
    openPartyEditModal: function(id){
        $.blockUI({message: '<h3>Processing...</h3>'});
        $('#examplePartyModal2').show();
        PartyService.getPartyById(id);
        $.unblockUI();
    },
    getPartyById: function(id){
        RestClient.get('party_by_id?id='+id, function (data) {
            localStorage.setItem('selected_party', JSON.stringify(data));
            $('input[name="name"]').val(data.name);
            $('input[name="abbreviation"]').val(data.abbreviation);
            $('input[name="id"]').val(data.id);
            $.unblockUI();
        }, function (xhr, status, error) {
            console.error('Error fetching data');
            $.unblockUI();
        });
    },
    addParty: function(party){
        $.blockUI({message: '<h3>Processing...</h3>'});
        RestClient.post('party', party, function(response){
            toastr.success("Party added successfully!");
            $.unblockUI();
            PartyService.closeModal();
            PartyService.generatePartiesDatatable();
        }, function(xhr, status, error){
            console.error("Error posting party: ", error);
        });
    },
    editParty: function(party){
        console.log(party);
        $.blockUI({ message: '<h3>Processing...</h3>' });
        RestClient.patch(`party/${party.id}`, party, function (data) {
            $.unblockUI();
            toastr.success("Party edited successfully!");
            PartyService.closeModal();
            PartyService.generatePartiesDatatable();
        }, function (xhr, status, error) {
            PartyService.closeModal();
            console.error("Error: ", error);
            $.unblockUI();
        });
    },
    deleteParty: function(){
        RestClient.delete('party/' + $("#delete-party-id").val(), null, function(response){
            PartyService.closeModal()
            toastr.success(response.message);
            PartyService.generatePartiesDatatable();
        }, function(response){
            PartyService.closeModal()
            toastr.error(response.message);
        })
    },
    openPartyConfirmationDialog: function(party){
        party = JSON.parse(party);
        $("#delete-party-body").html(
        "Do you want to delete party: " + party.name
        );
        $("#delete-party-id").val(party.id);
    },
    closeModal: function(){
       const modals = ['#examplePartyModal', '#examplePartyModal2', '#examplePartyModal3', '#examplePartyModal4'];
       modals.forEach(modalId=>{
            const modalEl = document.querySelector(modalId);
            const modal = bootstrap.Modal.getInstance(modalEl);
            if(modal){
                modal.hide();
            }
       });
    }
}