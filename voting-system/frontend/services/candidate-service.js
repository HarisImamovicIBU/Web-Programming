var CandidateService={
    init: function () {
        $("#add-candidate-form").validate({
             rules: {
                name: 'required',
                age: {
                    required: true,
                    digits: true,
                    min: 18,
                    max: 100
                },
                party_id: {
                    required: true,
                    digits: true,
                },
                motto: {
                    required: true,
                }
            },
            messages: {
                name: 'Please enter the candidate\'s name',
                age:{
                    required: 'Please enter the candidate\'s age',
                    digits: 'Please enter a number',
                    min: 'Candidate must be at least 18 years old',
                    max: 'Candidate must be younger than 100'
                },
                party_id: {
                    required: 'Please enter a valid party ID',
                    digits: 'Please enter a number'
                },
                motto: {
                    required: 'Please enter the candidate\'s motto',
                }
            },
            submitHandler: function (form) {
              let candidate = Object.fromEntries(new FormData(form).entries());
              CandidateService.addCandidate(candidate);
              form.reset();
            },
          });
       
        $("#edit-candidate-form").validate({
             rules: {
                name: 'required',
                age: {
                    required: true,
                    digits: true,
                    min: 18,
                    max: 100
                },
                party_id: {
                    required: true,
                    digits: true,
                },
                motto: {
                    required: true,
                }
            },
            messages: {
                name: 'Please enter the candidate\'s name',
                age:{
                    required: 'Please enter the candidate\'s age',
                    digits: 'Please enter a number',
                    min: 'Candidate must be at least 18 years old',
                    max: 'Candidate must be younger than 100'
                },
                party_id: {
                    required: 'Please enter a valid party ID',
                    digits: 'Please enter a number'
                },
                motto: {
                    required: 'Please enter the candidate\'s motto',
                }
            },
            submitHandler: function (form) {
              let candidate = Object.fromEntries(new FormData(form).entries());
              CandidateService.editCandidate(candidate);
            },
        });

        CandidateService.getAllCandidates();
    },
    getAllCandidates: function () {
        const token = localStorage.getItem("user_token");
        if(!token){
            window.location.replace("index.html");
        }
        const user = Utils.parseJwt(token).user;
        if(user && user.role){
            RestClient.get("parties", function(parties){
                var partyMap = new Map();
                parties.forEach(party=>{
                    partyMap.set(party.id, party.name);
                });
                RestClient.get("candidates", function(data){
                    data.forEach(candidate=>{
                        candidate.party_name = partyMap.get(candidate.party_id);
                    });
                    Utils.datatable("candidatesTable",[
                        {data: 'name', title: 'Name'},
                        {data: 'age', title: 'Age'},
                        {data: 'party_name', title: 'Political party'},
                        {data: 'motto', title: 'Motto'},
                        {title: 'Actions',
                            render: function(data, type, row, meta){
                                const rowStr = encodeURIComponent(JSON.stringify(row));
                                return `<div class="d-flex flex-wrap justify-content-center gap-2">
                                        <button type="button" class="btn btn-success btn-sm" id="admin-view-button" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="CandidateService.populateViewMoreModal('${row.id}')">View</button>
                                        <button type="button" class="btn btn-info btn-sm" id="admin-edit-button" data-bs-toggle="modal" data-bs-target="#exampleModal2" onclick="CandidateService.openEditModal('${row.id}')">Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm" id="admin-delete-button" data-bs-toggle="modal" data-bs-target="#exampleModal3" onclick="CandidateService.openConfirmationDialog(decodeURIComponent('${rowStr}'))">Delete</button>
                                    </div>`;
                            }
                            }
                    ], data, 10);
                    if(user.role === Constants.USER_ROLE){
                        var table = $("#candidatesTable").DataTable();
                        table.column(4).visible(false);
                    }
                }, function(xhr, status, error){
                    console.error("Error fetching candidate data: ", error);
                });
            });
        }
    },
    getCandidateById : function(id) {
        RestClient.get('candidate_by_id?id='+id, function (data) {
            localStorage.setItem('selected_candidate', JSON.stringify(data));
            $('input[name="name"]').val(data.name);
            $('input[name="age"]').val(data.age);
            $('input[name="party_id"]').val(data.party_id);
            $('input[name="motto"]').val(data.motto);
            $('input[name="id"]').val(data.id);
            $.unblockUI();
        }, function (xhr, status, error) {
            console.error('Error fetching data');
            $.unblockUI();
        });
    }, 
    addCandidate: function(candidate){
        $.blockUI({message: '<h3>Processing...</h3>'});
        RestClient.post('candidate', candidate, function(response){
            toastr.success("Candidate added successfully!");
            $.unblockUI();
            CandidateService.getAllCandidates();
            CandidateService.closeModal();
        }, function(response){
            CandidateService.closeModal();
            $.unblockUI();
            toastr.error(response.message);
        });
    },
    deleteCandidate: function () {
        RestClient.delete('candidate/' + $("#delete-candidate-id").val(), null, function(response){
            CandidateService.closeModal()
            toastr.success(response.message);
            CandidateService.getAllCandidates();
        }, function(response){
            CandidateService.closeModal()
            toastr.error(response.message);
        })
    },
    editCandidate: function(candidate){
        console.log(candidate);
        $.blockUI({ message: '<h3>Processing...</h3>' });
        RestClient.patch(`candidate/${candidate.id}`, candidate, function (data) {
            $.unblockUI();
            toastr.success("Candidate edited successfully!");
            CandidateService.closeModal();
            CandidateService.getAllCandidates();
        }, function (xhr, status, error) {
            CandidateService.closeModal();
            console.error("Error: ", error);
            $.unblockUI();
        });
    },
    openEditModal: function(id){
        $.blockUI({message: '<h3>Processing...</h3>'});
        $('#exampleModal2').show();
        CandidateService.getCandidateById(id);
        $.unblockUI();
    },
    populateViewVotesModal: function(){
        $("#admin-row-view-votes").empty();
        RestClient.get('candidates', function(candidates){
            candidates.forEach(candidate => {
                $("#admin-row-view-votes").append(
                    `<tr>
                        <td>${candidate.name}</td>
                        <td>${candidate.vote_count}</td>
                     </tr>`
                );
            });
        }, function(xhr, status, error){
            console.error("Error fetching candidate data: ", error);
        });
    },
    populateViewMoreModal: function(id){
        $("#admin-row-view-more").empty();
        RestClient.get(`candidate/${id}`, function(candidate){
            $("#admin-row-view-more").append(`
                <tr>
                    <td>${candidate.name}</td>
                    <td>${candidate.age}</td>
                    <td>${candidate.party_id}</td>
                    <td>${candidate.motto}</td>
                </tr>
            `);
        }, function(xhr, status, error){
            console.error("Error fetching candidate by id: ", error);
        });
    },
    openConfirmationDialog: function(candidate){
        candidate = JSON.parse(candidate);
        $("#delete-candidate-body").html(
        "Do you want to delete candidate: " + candidate.name
        );
        $("#delete-candidate-id").val(candidate.id);
    },
    closeModal: function(){
       const modals = ['#exampleModal', '#exampleModal2', '#exampleModal3', '#exampleModal4'];
       modals.forEach(modalId=>{
            const modalEl = document.querySelector(modalId);
            const modal = bootstrap.Modal.getInstance(modalEl);
            if(modal){
                modal.hide();
            }
       });
    }
};