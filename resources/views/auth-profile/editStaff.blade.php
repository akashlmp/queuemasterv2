@extends('common.layouts')
@section('content')
@extends('common.sidebar')
@extends('common.header')


<link rel="stylesheet" href="{{ asset('asset/css/userProfile.css') }}">

<main id="main" class="bgmain">
    <section class="SectionPadding">
        <div class="container overflowcontainer">
            <div class="row mb-3">
                <div class="col-xl-8 col-md-12 d-flex userProfileicon">
                    <nav aria-label="breadcrumb ">
                        <ol class="breadcrumb navbreadcrum">
                            <li class="breadcrumb-item"><a href="<?php echo url('dashboard'); ?>">
                                    <i class="fa fa-home userProfileicon" aria-hidden="true"></i></a>
                            </li>
                            <li class="breadcrumb-item " aria-current="page">
                                <a href="<?php echo url('staff-access-manage'); ?>" class="none">
                                    <div class="userProfileText text-center">Staff Access Management</div>
                                </a>
                            <li class="breadcrumb-item " aria-current="page">
                                <div class="userProfileText text-center">Edit Staff</div>
                            </li>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- card -->
            <div class=" card card-body">
                <form action="<?php echo route('updatePermissions', ['id' => $user->id]); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <!-- User Information -->

                    <div class="row m-0">
                        <div class="col-md-11 ps-0 pt-2 pb-4">
                            <div class="LeftGreenborder ps-4">
                                <h5 class="FormHeading mb-3"><b>User Information</b></h5>
                                <p class="FormPara">Name</p>
                                <div class="mb-3">
                                    <input type="text" id="name" name="name" value="<?php echo $user->company_person_name; ?>" class="form-control">
                                    <span class="text-danger" id="nameError"></span> <!-- JavaScript validation error message -->
                                </div>

                                <p class="FormPara">Email</p>
                                <div class="mb-2">
                                    <input type="email" id="email" name="email" value="<?php echo $user->email; ?>" class="form-control emaileditstaffclass" readonly disabled>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Permission Section -->
                    <div class="row m-0">
                        <div class="col-md-12 ps-0 pt-2 pb-4">
                            <div class="LeftGreenborder ps-4">
                                <h4 class="FormHeading"><b>Permission</b></h4>
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead class="table">
                                            <tr>
                                                <!-- Table headings for permissions -->
                                                <th class="border-0 py-0 tableheadFirst">
                                                    <h6 class="addstaffheading"></h6>
                                                </th>
                                                <th class="border-0 py-0">
                                                    <h6 class="addstaffheading">No Access</h6>
                                                </th>
                                                <th class="border-0 py-0">
                                                    <h6 class="addstaffheading">Read Only</h6>
                                                </th>
                                                <th class="border-0 py-0">
                                                    <h6 class="addstaffheading">Full Access</h6>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php //foreach($commonModules as $commonModule): 
                                            ?>
                                            <?php
                                            // $permissionsArray = isset($permissions[0]->common_module_id) ? json_decode($permissions[0]->common_module_id, true) : [];
                                            // $permissionValue = 0; // Default to no access
                                            // foreach ($permissionsArray as $permission) {
                                            //     if ($permission['module_id'] == $commonModule->id) {
                                            //         $permissionValue = $permission['permission'];
                                            //         break;
                                            //     }
                                            // }

                                            ?>
                                            <!-- <tr>
                                    <td class="border-0 d-flex">
                                        <div>
                                           
                                            <h3 class="mt-2 mb-0 Ruletextdata"><?php //echo $commonModule->name; 
                                                                                ?></h3>
                                        </div>
                                    </td>
                                    <td class="border-0 text-center">
                                        <input type="radio" name="common_<?php //echo $commonModule->id; 
                                                                            ?>" value="no_access" id="no_access_<?php //echo $commonModule->id; 
                                                                                                                                                ?>" <?php //echo $permissionValue == 0 ? 'checked' : ''; 
                                                                                                                                                                                    ?>>
                                    </td>
                                    <td class="border-0 text-center">
                                        <input type="radio" name="common_<?php //echo $commonModule->id; 
                                                                            ?>" value="read_only" id="read_only_<?php //echo $commonModule->id; 
                                                                                                                                                ?>" <?php //echo $permissionValue == 1 ? 'checked' : ''; 
                                                                                                                                                                                    ?>>
                                    </td>
                                    <td class="border-0 text-center">
                                        <input type="radio" name="common_<?php //echo $commonModule->id; 
                                                                            ?>" value="full_access" id="full_access_<?php //echo $commonModule->id; 
                                                                                                                                                    ?>" <?php //echo $permissionValue == 2 ? 'checked' : ''; 
                                                                                                                                                                                        ?>>
                                    </td>
                                </tr> -->
                                            <?php //endforeach; 
                                            ?>
                                            <?php
                                            $i = 0;
                                            $data = json_decode($permissions[0]->queue_room_access, true);
                                            $indices = array_keys($data);
                                            ?>
                                            <?php foreach ($rooms as $room) : ?>
                                                <tr>
                                                    <td colspan="4">
                                                        <h4 class="mt-2 mb-0 FormHeading"><b><?php echo $room->queue_room_name; ?></b></h4>
                                                    </td>
                                                </tr>
                                                <?php
                                                $j = 0;
                                                ?>

                                                <?php foreach ($modules as $module) :
                                                    $permission_for_module = null;
                                                ?>

                                                    <?php if (isset($indices[$i]) && isset($data[$indices[$i]])) : ?>
                                                        <?php
                                                        $roomData = $data[$indices[$i]];



                                                        if (isset($roomData[$j]['permission'])) {
                                                            $permission_for_module = $roomData[$j]['permission'];
                                                        }
                                                        ?>
                                                    <?php endif; ?>

                                                    <tr>
                                                        <td class="border-0 d-flex">
                                                            <div>
                                                                <h6 class="mt-2 mb-0 Ruletextdata"><?php echo $module->name; ?></h6>
                                                            </div>
                                                        </td>

                                                        <td class="border-0 text-center">
                                                            <input type="radio" name="permission_<?php echo $room->id ?><?php echo $module->id; ?>" value="no_access" id="no_access_<?php echo $room->id ?>_<?php echo $module->id ?>_<?php echo $i; ?>" <?php echo $permission_for_module == 0 ? 'checked' : ''; ?>>
                                                        </td>
                                                        <td class="border-0 text-center">
                                                            <input type="radio" name="permission_<?php echo $room->id ?><?php echo $module->id; ?>" value="read_only" id="read_only_<?php echo $room->id ?>_<?php echo $module->id ?>_<?php echo $i; ?>" <?php echo $permission_for_module == 1 ? 'checked' : ''; ?>>
                                                        </td>
                                                        <td class="border-0 text-center">
                                                            <input type="radio" name="permission_<?php echo $room->id ?><?php echo $module->id; ?>" value="full_access" id="full_access_<?php echo $room->id ?>_<?php echo $module->id ?>_<?php echo $i; ?>" <?php echo $permission_for_module == 2 ? 'checked' : ''; ?>>
                                                        </td>

                                                    </tr>
                                                    <?php
                                                    $j++;
                                                    ?>
                                                <?php endforeach; ?>
                                                <?php
                                                $i++;
                                                ?>
                                            <?php endforeach; ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- button -->
                    <div class="row m-0 mt-2">
                        <div class="col-md-12 ps-0 pt-2 pb-4 ">
                            <div class=" d-flex align-items-center justify-content-end">
                                <button class="btn bsb-btn-2xl subbtnbtn submitbtn d-flex align-items-center justify-content-center" type="submit" id="savesubmitButton">
                                    Save
                                    <span class="material-symbols-outlined ms-2">save</span>
                                </button>

                            </div>
                        </div>
                    </div>
                    <!-- end button -->
                </form>
            </div>
        </div>
    </section>
</main>
<script src="{{ asset('asset/js/userProfile.js') }}" type="text/javascript"></script>

<script>
    // $(document).ready(function() {
    //     // Disable submit button by default


    //     // Name validation
    //     $('#name').on('keyup', function() {
    //         var name = $(this).val().trim();
    //         if (name.length > 0) {
    //             console.log('Valid name: ' + name);
    //             $('#nameError').text('');
    //             $('#savesubmitButton').prop('disabled', false);
    //         } else {
    //             console.log('Invalid name');
    //             $('#nameError').text('Name is required.');
    //             $('#savesubmitButton').prop('disabled', true);
    //         }
    //     });
    // });
</script>

@endsection