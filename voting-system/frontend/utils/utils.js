var Utils = {
    init_spapp: () => {
        var app = $.spapp({
            templateDir: "./pages/",
            defaultView: "#home"
        });
        app.route({
            view: "home",
            load: "home.html"
        });
        app.route({
            view: "candidates",
            load: "candidates.html"
        });
        app.route({
            view: "vote",
            load: "vote.html"
        });
        app.route({
            view: "contact",
            load: "contact.html"
        });
        $(document).on("click", "a.nav-link", function () {
            setTimeout(()=>{
                window.scrollTo(0, 0);
            }, 1);
        });
        app.run();
    },
    datatable: function (table_id, columns, data, pageLength=15) {
        if ($.fn.dataTable.isDataTable("#" + table_id)) {
            $("#" + table_id)
            .DataTable()
            .destroy();
        }
        $("#" + table_id).DataTable({
          data: data,
          columns: columns,
          pageLength: pageLength,
          lengthMenu: [2, 5, 10, 15, 25, 50, 100, "All"],
        });
    },
    parseJwt: function(token) {
        if (!token) return null;
        try {
          const payload = token.split('.')[1];
          const decoded = atob(payload);
          return JSON.parse(decoded);
        } catch (e) {
          console.error("Invalid JWT token", e);
          return null;
        }
    }
    /*
    fetchDataAndCreateDatatable: () => {
        $.ajax({
            url: "data/CANDIDATES.json",
            dataType: "json",
            success: (data) => {
                let tableBody = $("#candidatesTable tbody");
                tableBody.empty();
                let counter = 0;
                data.candidates.forEach((candidate) => {
                    let row = `<tr>
                                <td>${candidate.name}</td>
                                <td>${candidate.age}</td>
                                <td>${candidate.political_party}</td>
                                <td>${candidate.motto}</td>
                                <td class="align-middle text-center">
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <button type="button" class="btn btn-success btn-sm" id="admin-view-button" data-bs-toggle="modal" data-bs-target="#exampleModal">View</button>
                                        <button type="button" class="btn btn-info btn-sm" id="admin-edit-button" data-bs-toggle="modal" data-bs-target="#exampleModal2">Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm" id="admin-delete-button" data-bs-toggle="modal" data-bs-target="#exampleModal3">Delete</button>
                                    </div>
                                </td>
                           </tr>`;
                    tableBody.append(row);
                });
                console.log(counter);
                if($.fn.DataTable.isDataTable("#candidatesTable")){
                    $("#candidatesTable").DataTable().clear().destroy();
                }
                $("#candidatesTable").DataTable();
            },
            error: (xhr, Status, error) => {
                console.error("Failed to load candidates:", error);
            }
        });
    },
    fetchDataAndCreateSelect2: ()=>{
        $.ajax({
            url: "data/CANDIDATES.json",
            dataType: "json",
            success: (data)=>{
                let selectParty = $("#partySelect");
                let selectCandidates = $("#candidateSelect");
                selectParty.empty();
                let uniqueParties = new Set();
                data.candidates.forEach((candidate)=>{
                    uniqueParties.add(candidate.political_party);
                });
                uniqueParties.forEach((party) => {
                    selectParty.append(`<option value="${party}">${party}</option>`);
                });
                $("#partySelect").on("change", function(e){
                    let selectedParty = $(this).val();
                    $("#toAdd").empty();
                    $("#toAdd").append(` from ${selectedParty}`);
                    selectCandidates.empty();
                    data.candidates.forEach((candidate)=>{
                        if(candidate.political_party===selectedParty){
                            selectCandidates.append(`<option value="${candidate.name}">${candidate.name}</option>`);
                        }
                    selectCandidates.select2();
                    });

                })
            }, 
            error: (xhr, Status, error)=>{
                console.error("Failed to load candidates: ", error);
            }
        });
    }*/
};