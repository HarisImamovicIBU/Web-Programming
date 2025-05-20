var InquiryService={
    init: function(){
        $("#inquiry-form").validate({
            rules: {
                comment: {
                    required: true,
                    minlength: 35,
                    maxlength: 350
                }
            },
            messages: {
                comment: {
                    required: 'Please enter your comment.',
                    minlength: 'Your comment should be at least 35 characters long',
                    maxlength: 'Your comment should be at most 350 characters long'
                }
            },
            submitHandler: function(form){
                let inquiry = Object.fromEntries(new FormData(form).entries());
                InquiryService.submitInquiry(inquiry);
                form.reset();
            }
        });
    },
    submitInquiry: function(inquiry){
        const token = localStorage.getItem("user_token");
        if(token){
            const user = Utils.parseJwt(token).user;
            if(user && user.role){
                $.blockUI({message: '<h3>Processing...</h3>'});
                    RestClient.post('inquiry', {user_id: user.id, comment: inquiry.comment}, function(response){
                        $.unblockUI();
                        toastr.success("Inquiry added successfully!");
                    }, function(xhr,status,error){
                        console.error("Error: ", error);
                });
            }else{
                return window.location.replace("index.html");
            }
        }else{
            return window.location.replace("index.html");
        }
    },
    populateViewInquiriesModal: function(){
        $("#admin-row-view-inquiries").empty();
        RestClient.get('inquiries', function(inquiries){
            inquiries.forEach(inquiry => {
                $("#admin-row-view-inquiries").append(
                    `<tr>
                        <td>${inquiry.user_id}</td>
                        <td>${inquiry.comment}</td>
                     </tr>
                    `
                );
            });
        },function(xhr, status, error){
            console.error("Error fetching inquiries: ", error);
        })
    }
}