<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel Api with Axios</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <style>
        body {
            padding-top: 50px;
        }

    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h4>Posts</h4>
                <span id="successMsg"></span>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">

                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <span id="successMsg"></span>
                <h4>Create Post</h4>
                <form name="myForm">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" placeholder="title">
                        <span id="titleError"></span>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="4"
                            placeholder="Description"></textarea>
                        <span id="descError"></span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Post</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form name="editForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" placeholder="title">
                            <span id="editTitleError"></span>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="4"
                                placeholder="Description"></textarea>
                            <span id="editDescError"></span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    {{-- axios link --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        var tableBody = document.getElementById('tableBody');
        var titleList = document.getElementsByClassName("titleList");
        var descList = document.getElementsByClassName("descList");
        var idList = document.getElementsByClassName("idList");
        var btnList = document.getElementsByClassName("btnList");
        console.log(idList);
        //Read
        axios.get('/api/posts')
            .then(function(response) {

                response.data.posts.forEach(item => {
                    displayData(item);
                });

                //console.log(response.data);
            })
            .catch(function(error) {
                // handle error
                //console.log(error);
            })
            .then(function() {
                // always executed
            });

        //Create
        var myForm = document.forms['myForm'];
        var titleInput = myForm['title'];
        var descriptionInput = myForm['description'];
        var titleError = document.getElementById('titleError');
        var descError = document.getElementById('descError');
        var editTitleError = document.getElementById('editTitleError');
        var editDescError = document.getElementById('editDescError');

        myForm.onsubmit = function(event) {
            event.preventDefault();
            axios.post('/api/posts', {
                    title: titleInput.value,
                    description: descriptionInput.value
                })
                .then(function(response) {
                    console.log(response.data.msg);

                    if (response.data.msg) {
                        alertMsg(response.data.msg);
                        myForm.reset();
                        displayData(response.data.post);

                        titleError.innerHTML = "";
                        descError.innerHTML = "";
                    } else {

                        if (titleInput.value == "") {
                            titleError.innerHTML =
                                '<i class="text-danger">' +
                                response.data.errors.title +
                                '</i>';
                        } else {
                            titleError.innerHTML = "";
                        }
                        if (descriptionInput.value == "") {
                            descError.innerHTML =
                                '<i class="text-danger">' +
                                response.data.errors.description +
                                '</i>';
                        } else {
                            descError.innerHTML = '';
                        }
                    }
                })
                .catch(function(error) {
                    //console.log(error);
                });
        }

        //Edit & Update
        var editForm = document.forms['editForm'];
        var editTitleInput = editForm['title'];
        var editDescriptionInput = editForm['description'];
        var postIdToUpdate;
        // var oldTitle;
        // var oldDescription;
        var oldId;

        //Edit
        function postEdit(id) {
            postIdToUpdate = id;
            axios.get('/api/posts/' + id)
                .then(function(response) {
                    console.log(response);
                    editTitleInput.value = response.data.post.title;
                    editDescriptionInput.value = response.data.post.description;

                    // oldTitle = response.data.title;
                    // oldDescription = response.data.description;
                    oldId = response.data.post.id;

                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
                .then(function() {
                    // always executed
                });
        }

        //Update
        editForm.onsubmit = function(event) {
            event.preventDefault();
            axios.put('/api/posts/' + postIdToUpdate, {
                    title: editTitleInput.value,
                    description: editDescriptionInput.value
                })
                .then(function(response) {
                    console.log(response);
                    alertMsg(response.data.msg);
                    if (response.data.msg) {
                        $('#editModal').modal('hide');
                        for (var i = 0; i < idList.length; i++) {
                            if (idList[i].innerHTML == oldId) {
                                titleList[i].innerHTML = editTitleInput.value;
                                descList[i].innerHTML = editDescriptionInput.value;
                            }
                        }
                        editTitleError.innerHTML = "";
                        editDescError.innerHTML = "";

                    } else {
                        if (editTitleInput.value == "") {
                            editTitleError.innerHTML =
                                '<i class="text-danger">' +
                                response.data.errors.title +
                                '</i>';
                        } else {
                            editTitleError.innerHTML = "";
                        }
                        if (editDescriptionInput.value == "") {
                            editDescError.innerHTML =
                                '<i class="text-danger">' +
                                response.data.errors.description +
                                '</i>';
                        } else {
                            editDescError.innerHTML = '';
                        }
                    }

                })
                .catch(function(error) {
                    console.log(error);
                });
        };

        //Delete
        function deleteBtn(id) {
            if (confirm("Are you sure to delete?")) {
                axios.delete('/api/posts/' + id)
                    .then(function(response) {
                        console.log(response.data.deletedPost.id);
                        alertMsg(response.data.msg);
                        for (var i = 0; i < idList.length; i++) {
                            if (idList[i].innerHTML == response.data.deletedPost.id) {
                                idList[i].style.display = "none";
                                titleList[i].style.display = "none";
                                descList[i].style.display = "none";
                                btnList[i].style.display = "none";
                            }
                        }

                    })
                    .catch(function(error) {
                        // handle error
                        console.log(error);
                    })
                    .then(function() {
                        // always executed
                    });
            }

        }

        //Desplay data
        function displayData(data) {
            tableBody.innerHTML +=
                '<tr>' +
                '<td class="idList">' + data.id + '</td>' +
                '<td class="titleList">' + data.title + '</td>' +
                '<td class="descList">' + data.description + '</td>' +
                '<td class="btnList">' +
                '<button class="btn btn-success btn-sm" data-toggle="modal" data-target="#editModal" onclick="postEdit(' +
                data.id + ')">Edit</button>' +
                '<button onclick="deleteBtn(' + data.id +
                ')" class="btn btn-danger btn-sm">Delete</button>' +
                '</td>' +
                '</tr>';
        }

        //alert msg
        function alertMsg(msg) {
            document.getElementById('successMsg').innerHTML =
                '<div class="alert alert-success alert-dismissible show" role="alert"><strong>' +
                msg +
                '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }
    </script>
</body>

</html>
