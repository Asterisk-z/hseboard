<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute } from 'vue-router'
import { router } from '@/router';

import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import { useOrganizationStore } from '@/stores/organizationStore';
import { useAuthStore } from '@/stores/auth';
import { useOpenLinksStore } from '@/stores/openLinks';
import { usePermitToWorkStore } from '@/stores/permitToWorkStore';
import moment from 'moment'
import Swal from 'sweetalert2'


const page = ref({ title: 'Update Permit Intent' });
const breadcrumbs = ref([
    {
        text: 'Dashboard',
        disabled: false,
        href: '/dashboard'
    },
    {
        text: 'Permit To Work',
        disabled: false,
        href: '/work-permit'
    },
    {
        text: 'Update',
        disabled: true,
        href: '#'
    }
]);

const route = useRoute()
const authStore = useAuthStore();
const organizationStore = useOrganizationStore();
const openLinks = useOpenLinksStore();
const permitToWorkStore = usePermitToWorkStore();


onMounted(() => {
    permitToWorkStore.getAcceptedPermit(route.params.permit_id as string)
    permitToWorkStore.getActiveJHA()


    openLinks.getPriorities();
});


const getPriorities : any  = computed(() => (openLinks.priorities));
const computedIndex = (index : any) => ++index;

const getAuthUser : any  = computed(() => (authStore.loggedUser));
const getAcceptedPermit: any   = computed(() => (permitToWorkStore.acceptedPermit));
const getActiveJHA : any  = computed(() => (permitToWorkStore.activeJHA));
const getActiveOrg : any  = computed(() => (organizationStore.getActiveOrg()));
const isLoggedInUserOwnsActionOrg : any  = computed(() => (getAuthUser.value?.id == getActiveOrg.value?.user_id));
const isIssuerOrg : any  = computed(() => (getAcceptedPermit.value?.issuer_organization_id == getActiveOrg.value?.id));
const isHolderOrg : any  = computed(() => (getAcceptedPermit.value?.holder_organization_id == getActiveOrg.value?.id));
const isSameOrg : any  = computed(() => (getAcceptedPermit.value?.holder_organization_id == getAcceptedPermit.value?.issuer_organization_id));

const getHoldingMembers : any  = computed(() => (permitToWorkStore.holdingMembers));
const getInspecteeMembers : any  = computed(() => (permitToWorkStore.inspecteeMembers));

const isLoggedInUserIsPermitHolder : any  = computed(() => (getAuthUser.value?.id == getAcceptedPermit.value?.holder?.id));
const isLoggedInUserIsPermitIssuer : any  = computed(() => (getAuthUser.value?.id == getAcceptedPermit.value?.issuer?.id));

watch(
  () => isHolderOrg.value,
  (value) => {
    if(value) {  
        permitToWorkStore.getHoldingMembers(route.params.permit_id as string)
    }
    
      if (getAcceptedPermit.value?.is_pending) {
        Swal.fire({
            title: 'Info!',
            text: 'Organization is yet to accept Inspection?',
            icon: 'info',
            confirmButtonText: 'Ok',
            showCancelButton: false,
            allowOutsideClick: false,
        }).then((result) => {
            
        });
    }
  }
)
watch(
  () => isIssuerOrg.value,
  (value) => {
    //   if (value) {
    //     permitToWorkStore.getInspecteeMembers(route.params.permit_id as string)
    //     }
      if (getAcceptedPermit.value?.is_pending) {
        Swal.fire({
            title: 'Info!',
            text: `${getAcceptedPermit.value?.holder_organization?.name} sent a request to inspect your organization, do you accept or reject`,
            icon: 'info',
            confirmButtonText: 'Accept',
            cancelButtonText: 'Reject',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            
                
        
                let objectValues = {
                    "organization_id": getActiveOrg.value?.uuid,
                    "permit_id": getAcceptedPermit.value?.uuid,
                    "action": result.isConfirmed ? 'accepted' : 'rejected',
                    "reason":  'reason',
                }
                        
            // permitToWorkStore.actionInspection(objectValues)
            //         .catch((error: any) => {
            //             throw error
            //         })
            //         .then((resp: any) => {
            //              permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
            //             return resp
            //         });



        });
    }
  }
)

//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------




const valid = ref(true);;
const formContainer = ref()
const loading = ref(false);
const setLoading = (value: boolean) => {
    loading.value = value;
}


const addMembersDialog = ref(false);
const setAddMembersDialog = (value: boolean) => {
    addMembersDialog.value = value;
}
const addRepresentativeDialog = ref(false);
const setAddRepresentativeDialog = (value: boolean) => {
    addRepresentativeDialog.value = value;
}


const viewReportDialog = ref(false);
const setViewReportDialog = (value: boolean) => {
    viewReportDialog.value = value;
}
const addInviteDialog = ref(false);
const setAddInviteDialog = (value: boolean) => {
    addInviteDialog.value = value;
}
const addMemberDialog = ref(false);
const setAddMemberDialog = (value: boolean) => {
    addMemberDialog.value = value;
}
const viewQuestionTeamDialog = ref(false);
const setSelectQuestionaireTeam = (value: boolean) => {
    viewQuestionTeamDialog.value = value;
}

const viewQuestionDialog = ref(false);
const setViewQuestionDialog = (value: boolean) => {
    viewQuestionDialog.value = value;
    if (value == false) selectItem({})
}
const viewFindingDialog = ref(false);
const setViewFindingDialog = (value: boolean, type: string = '') => {
    viewFindingDialog.value = value;
    stepFourFields.value.type = type;
    if (value == false) selectItem({})
}
const viewRecommendationDialog = ref(false);
const setViewRecommendationDialog = (value: boolean, type: string = '') => {
    viewRecommendationDialog.value = value;
    if (value == false) selectItem({})
}
const selectedItem = ref({} as any);
const selectItem = (item: any, action: string = '', extra: any = '') => {
    selectedItem.value = Object.assign({}, item.raw);

    switch (action) {
        case 'viewQuestion':
            setViewQuestionDialog(true)
            break;
        case 'viewFinding':
            setViewFindingDialog(true, extra)
            break;
        case 'viewRecommendation':
            setViewFindingDialog(true)
            break;
        // case 'edit':

        //     setEditDialog(true)
        //     break;
        // case 'delete':
        //     setDeleteDialog(true)
        //     break;
        default:
            break;
    }

}

//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
const fields = ref({
    supervisor: '',

    officers: [],
    nurses: [],
    workers: [],
    entrants: [],
    attendants: [],

    organization_id: getActiveOrg.value?.uuid,
});

const clearOtherUsers = () => {
    fields.value.officers = []
    fields.value.nurses = []
    fields.value.workers = []
    fields.value.entrants = []
    fields.value.attendants = []
}

const filteredSupervisor : any  = computed(() => (getHoldingMembers.value?.filter((member: any) => (member.id != fields.value?.supervisor))));

const getRepresentatives : any  = computed(() => (getAcceptedPermit.value?.all_representatives));
const getInspectors : any  = computed(() => (getAcceptedPermit.value?.all_inspectors));
const getMembers : any  = computed(() => (getAcceptedPermit.value?.team_members));

// const filteredgetInspecteeMembers : any  = computed(() => (getInspecteeMembers.value?.filter((member: any) => (member.id != fields.value?.leadRepresentative  && member.id != getAcceptedPermit.value?.lead_inspector?.user_id))));


const fieldRules: any = ref({
    supervisor: [
        (v: number) => !!v || 'Field is Required',
    ],
    inspectorMembers: [
        (v: number) => !!v || 'Field is Required',
    ],
    leadRepresentative: [
        (v: number) => !!v || 'Field is Required',
    ],
    representatives: [
        (v: number) => !!v || 'Field is Required',
    ],
    leadInvestigator: [
        (v: number) => !!v || 'Field is Required',
    ],
    teamMember: [
        (v: number) => !!v || 'Field is Required',
    ],
    groupChatName: [
        (v: string) => !!v || 'Field is Required',
    ],
})


const sendTeamMembers = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...fields.value }
             
        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "permit_id": getAcceptedPermit.value?.uuid,
            "supervisor": values?.supervisor,
            "officers": values?.officers,
            "nurses": values?.nurses,
            "workers": values?.workers,
            "entrants": values?.entrants,
            "attendants": values?.attendants,
        }

        const resp = await permitToWorkStore.setMembers(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setAddMembersDialog(false)
            permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
        }

        setLoading(false)
        setAddMembersDialog(false)



    } catch (error) {
        console.log(error)
        setLoading(false)
        setAddMembersDialog(false)
    }

}


const removeMember = async (member: any) => {

    try {
        setLoading(true)
        
        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "permit_id": getAcceptedPermit.value?.uuid,
            "team_member":  member
        }
        
        Swal.fire({
            title: 'Info!',
            text: 'Do you want to remove member?',
            icon: 'info',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                
                
                permitToWorkStore.removeMember(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
                        return resp
                    });


            }
        });


        setLoading(false)

    } catch (error) {
        console.log(error)
        setLoading(false)
    }

}
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------

const updateInspectionDialog = ref(false);
const setUpdateInspectionDialog = (value: boolean) => {
    updateInspectionDialog.value = value;
}

const stepDetailFields = ref({
    jobName: getAcceptedPermit.value?.job_title || '',
    jobCode: getAcceptedPermit.value?.job_code || '',
    locationName: getAcceptedPermit.value?.location_name || '',
    locationNumber: getAcceptedPermit.value?.location_id_no || '',
    contractorName: getAcceptedPermit.value?.contractor_name || '',
    requestDate: getAcceptedPermit.value?.requested_date || '',
    description: getAcceptedPermit.value?.description_of_work || '',
    start_date: getAcceptedPermit.value?.work_start_time || '',
    end_date: getAcceptedPermit.value?.work_end_time || '',
    organization_id: getActiveOrg.value?.uuid,
});

const stepDetailFieldRules: any = ref({
    name: [
        (v: string) => !!v || 'Field is Required',
    ],
    location: [
        (v: string) => !!v || 'Field is Required',
    ],
    description: [
        (v: string) => !!v || 'Field is Required',
    ],
    objective: [
        (v: string) => !!v || 'Field is Required',
    ],
    ppe: [
        (v: string) => !!v || 'Field is Required',
    ],
})

watch(
    () => getAcceptedPermit.value,
    (value) => {


        if (getAcceptedPermit.value?.job_title) {
            stepDetailFields.value.jobName = getAcceptedPermit.value?.job_title
            stepDetailFields.value.jobCode = getAcceptedPermit.value?.job_code
            stepDetailFields.value.locationName = getAcceptedPermit.value?.location_name
            stepDetailFields.value.locationNumber = getAcceptedPermit.value?.location_id_no
            stepDetailFields.value.contractorName = getAcceptedPermit.value?.contractor_name
            stepDetailFields.value.requestDate = getAcceptedPermit.value?.requested_date
            stepDetailFields.value.description = getAcceptedPermit.value?.description_of_work
            stepDetailFields.value.start_date = getAcceptedPermit.value?.work_start_time
            stepDetailFields.value.end_date = getAcceptedPermit.value?.work_end_time
        }
    }
)

const updatePermitDetails = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepDetailFields.value }


        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "permit_id": getAcceptedPermit.value?.uuid,
            "job_code": values?.jobCode,
            "job_title": values?.jobName,
            "location": values?.locationName,
            "location_id": values?.locationNumber,
            "contractor_name": values?.contractorName,
            "description": values?.description,
            "request_date": moment(values?.requestDate).format('YYYY-MM-DD HH:mm:ss'),
            "start_date":  moment(values?.start_date).format('YYYY-MM-DD HH:mm:ss'),
            "end_date": moment(values?.end_date).format('YYYY-MM-DD HH:mm:ss'),
        }

        const resp = await permitToWorkStore.updatePermit(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });


        if (resp?.message == 'success') {
            setLoading(false)

            setUpdateInspectionDialog(false)
            permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
        }

        setLoading(false)
        setUpdateInspectionDialog(false)



    } catch (error) {
        console.log(error)
        setUpdateInspectionDialog(false)

        setLoading(false)
    }

}


const actionRequestFormDialog = ref(false);
const setActionRequestFormDialog = (value: boolean) => {
    actionRequestFormDialog.value = value;
}


const requestFormField = ref({
    comment: "",
    status: "",
});

const requestFormFieldRules: any = ref({
    comment: [
        (v: string) => !!v || 'Field is Required',
    ],
    status: [
        (v: string) => !!v || 'Field is Required',
    ],
})

const actionRequestForm = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...requestFormField.value }


        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "permit_id": getAcceptedPermit.value?.uuid,
            "comment": values?.comment,
            "action": values?.status,
        }

        const resp = await permitToWorkStore.actionRequestForm(objectValues)
            .catch((error: any) => {
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setActionRequestFormDialog(false)
            permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
        }

        setLoading(false)
        setActionRequestFormDialog(false)

        requestFormField.value.comment = "";

    } catch (error) {
        console.log(error)
        setLoading(false)
        setActionRequestFormDialog(false)
    }

}

// const proceedInspection = async (member: any) => {

//     try {
//         setLoading(true)
        
//         let objectValues = {
//             "organization_id": getActiveOrg.value?.uuid,
//             "permit_id": getAcceptedPermit.value?.uuid,
//         }
        
//         Swal.fire({
//             title: 'Info!',
//             text: 'Do you want to proceed?',
//             icon: 'info',
//             confirmButtonText: 'Yes',
//             cancelButtonText: 'No',
//             showCancelButton: true,
//             allowOutsideClick: false,
//         }).then((result) => {
//             if (result.isConfirmed) {
                
                
//                 permitToWorkStore.proceedInspection(objectValues)
//                     .catch((error: any) => {
//                         throw error
//                     })
//                     .then((resp: any) => {
//                          permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
//                         return resp
//                     });


//             }
//         });


//         setLoading(false)

//     } catch (error) {
//         console.log(error)
//         setLoading(false)
//     }

// }
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------


const requestDocumentDialog = ref(false);
const setRequestDocumentDialog = (value: boolean) => {
    requestDocumentDialog.value = value;
}

const sendDocumentDialog = ref(false);
const setSendDocumentDialog = (value: boolean, item_id : any = '') => {
    sendDocumentDialog.value = value;
    stepTwoFields.value.document_id = item_id
}

const showRejectDialog = ref(false);
const setRejectReasonDialog = (value: boolean, item_id : any = '') => {
    showRejectDialog.value = value;
    stepTwoFields.value.document_id = item_id
}


const stepTwoFields = ref({
    title: "",
    document_id: "",
    description: "",
    comments: "",
    reason: "",
    files: '',

});




const files = ref([])
const images = ref([])
const previewImage = ref([] as any)

const selectImage = (image: any) => {
    stepTwoFields.value.files =  image.target.files
    images.value = image.target.files;
    previewImage.value = [];

    for (let index = 0; index < images.value.length; index++) {
        const element = images.value[index];
        previewImage.value.push(URL.createObjectURL(element) as string)
    }

}


//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------

const sendScheduleDialog = ref(false);
const setSendScheduleDialog = (value: boolean) => {
    sendScheduleDialog.value = value;
}
const sendJHADialog = ref(false);
const setSendJHADialog = (value: boolean, action: string = '') => {
    sendJHADialog.value = value;
}

const actionJHADialog = ref(false);
const setActionJHADialog = (value: boolean, action: string = '') => {
    actionJHADialog.value = value;
    stepThreeFields.value.status = action
}

const getSchedule : any  = computed(() => (getAcceptedPermit.value?.schedule));
const getJHA : any  = computed(() => (getAcceptedPermit.value?.jha));

const stepThreeFields = ref({
    date_time: "",
    comment: "",
    status: "",
    jha_id: "",
});

const stepThreeFieldRules: any = ref({
    date_times: [
        (v: string) => !!v || 'Field is Required',
    ],
})

const actionJHA = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepThreeFields.value }


        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "permit_id": getAcceptedPermit.value?.uuid,
            "comment": values?.comment,
            "action": values?.status,
        }

        const resp = await permitToWorkStore.actionJHA(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setActionJHADialog(false)
            permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
        }

        setLoading(false)
        setActionJHADialog(false)

        stepThreeFields.value.date_time = "";

    } catch (error) {
        console.log(error)
        setLoading(false)
        setActionJHADialog(false)
    }

}
const sendJHA = async (e: any) => {
    e.preventDefault();

    try {
        setLoading(true)

        const values = { ...stepThreeFields.value }


        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "permit_id": getAcceptedPermit.value?.uuid,
            "jha_id": values?.jha_id,
        }

        const resp = await permitToWorkStore.sendJHA(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setSendJHADialog(false)
            permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
        }

        setLoading(false)
        setSendJHADialog(false)
        stepThreeFields.value.jha_id = "";



    } catch (error) {
        console.log(error)
        setLoading(false)
        setSendJHADialog(false)
    }

}


const issuePermitFields = ref({
    start_date: "",
    end_date: "",
});

const issuePermitFieldRules: any = ref({
    start_date: [
        (v: string) => !!v || 'Field is Required',
    ],
    end_date: [
        (v: string) => !!v || 'Field is Required',
    ],
})
const sendIssuePermitDialog = ref(false);
const setSendIssuePermitDialog = (value: boolean, action: string = '') => {
    sendIssuePermitDialog.value = value;
}

const sendIssuePermit = async (value: any) => {
    value.preventDefault();

    try {
        setLoading(true)

        const values = { ...issuePermitFields.value }


        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "permit_id": getAcceptedPermit.value?.uuid,
            "start_date": moment(values?.start_date).format('YYYY-MM-DD HH:mm:ss'),
            "end_date":  moment(values?.end_date).format('YYYY-MM-DD HH:mm:ss'),  
        }

        const resp = await permitToWorkStore.sendIssuePermit(objectValues)
            .catch((error: any) => {
                console.log(error)
                throw error
            })
            .then((resp: any) => {
                return resp
            });

        if (resp?.message == 'success') {
            setLoading(false)
            setSendIssuePermitDialog(false)
            permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
        }

        setLoading(false)
        setSendIssuePermitDialog(false)
        issuePermitFields.value.start_date = "";
        issuePermitFields.value.end_date = "";



    } catch (error) {
        console.log(error)
        setLoading(false)
        setSendIssuePermitDialog(false)
    }

}


//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------

const sendForDeclaration = async (member: any) => {

    try {
        setLoading(true)
        
        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "permit_id": getAcceptedPermit.value?.uuid,
        }
        
        Swal.fire({
            title: 'Info!',
            text: 'Are you sure?',
            icon: 'info',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                
                
                permitToWorkStore.sendForDeclaration(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
                        return resp
                    });


            }
        });


        setLoading(false)

    } catch (error) {
        console.log(error)
        setLoading(false)
    }

}

const sendDeclaration = async (member: any) => {

    try {
        setLoading(true)
        
        let objectValues = {
            "organization_id": getActiveOrg.value?.uuid,
            "permit_id": getAcceptedPermit.value?.uuid,
        }
        
        Swal.fire({
            title: 'Info!',
            text: `I declare that I have been briefed about the ${getAcceptedPermit.value?.job_title} at  ${getAcceptedPermit.value?.location_name}  to commence on  ${getAcceptedPermit.value?.work_start_time} . I understand the hazards and risks and I agree to comply with all safety requirements for the job?`,
            icon: 'info',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                
                
                permitToWorkStore.sendDeclaration(objectValues)
                    .catch((error: any) => {
                        throw error
                    })
                    .then((resp: any) => {
                        permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
                        return resp
                    });


            }
        });


        setLoading(false)

    } catch (error) {
        console.log(error)
        setLoading(false)
    }

}

const questionTab = ref('question-tab-0');
const questionOldTab = ref('');

function changeQuestionTab(e: string) {
    questionTab.value = e;
}
// console.log(questionTab.value)
const getQuestions : any  = computed(() => (getAcceptedPermit.value?.questions));

const sendCommentDialog = ref(false);
const setSendCommentDialog = (value: boolean, item_id: string = '', topic_id: string = '', question = {}) => {
    sendCommentDialog.value = value;
    stepFourFields.value.question_id = item_id
    stepFourFields.value.topic_id = topic_id
}

const stepFourFields = ref({
    question_id: "",
    topic_id: "",
    comments: "",
    type: "",
});


const stepFourFieldRules: any = ref({
    response: [
        (v: string) => !!v || 'Field is Required',
    ],
    comments: [
        (v: string) => !!v || 'Field is Required',
    ],
})


// const sendQuestionResponse = async (question_id: number, response: string, topic : number) => {
    

//     try {

//         let objectValues = {
//             "organization_id": getActiveOrg.value?.uuid,
//             "permit_id": getAcceptedPermit.value?.uuid,
//             "inspection_question_id": question_id,
//             "response": response,
//         }

//         const resp = await permitToWorkStore.sendQuestionResponse(objectValues)
//             .catch((error: any) => {
//                 console.log(error)
//                 throw error
//             })
//             .then((resp: any) => {
//                 return resp
//             });

//         if (resp?.message == 'success') {
//             permitToWorkStore.ongoingInspection.questions.forEach((question: any, index: number) => {
//                 if(question.id == topic) {
//                      question.questions.forEach((main_question: any, subIndex: number) => {
//                         if(main_question.id == question_id) {
//                             permitToWorkStore.ongoingInspection.questions[index].questions[subIndex].answer = response
//                         }
//                      })
//                 }
//             })
            
//         }


//     } catch (error) {
        
//     }

// }


// const sendQuestionComment = async (e: any) => {
//     e.preventDefault();

//     try {
//         setLoading(true)

//         const values = { ...stepFourFields.value }


//         let objectValues = {
//             "organization_id": getActiveOrg.value?.uuid,
//             "permit_id": getAcceptedPermit.value?.uuid,
//             "inspection_question_id": values?.question_id,
//             "comment": values?.comments,
//         }

//         const resp = await permitToWorkStore.sendQuestionComment(objectValues)
//             .catch((error: any) => {
//                 console.log(error)
//                 throw error
//             })
//             .then((resp: any) => {
//                 return resp
//             });

        
//         if (resp?.message == 'success') {
//             permitToWorkStore.ongoingInspection.questions.forEach((question: any, index: number) => {
//                 if(question.id == values?.topic_id) {
//                      question.questions.forEach((main_question: any, subIndex: number) => {
//                         if(main_question.id == values?.question_id) {
//                             permitToWorkStore.ongoingInspection.questions[index].questions[subIndex].comment = values?.comments
//                         }
//                      })
//                 }
//             })
            
//         }

//         setLoading(false)
//         setSendCommentDialog(false)

//         stepFourFields.value.comments = "";

//     } catch (error) {
//         console.log(error)
//         setLoading(false)
//         setSendCommentDialog(false)
//     }

// }


//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------


const getFindings : any  = computed(() => (getAcceptedPermit.value?.findings));
const getActions : any  = computed(() => (getAcceptedPermit.value?.actions));


const stepFiveFields = ref({
    title: "",
    description: "",
    assigneeId: "",
    priorityId: "",
    end_date: "",
    start_date: "",
    detail: "",
    organization_id: getActiveOrg.value?.uuid,
    
});

const stepFiveFieldRules : any = ref({
    priorityId: [
        (v: string) => !!v || 'Priority is required',
    ],
    assigneeId: [
        (v: string) => !!v || 'User is required',
    ],
    title: [
        (v: string) => !!v || 'Title  is required',
    ],
    detail: [
        (v: string) => !!v || 'Title  is required',
    ],
    description: [
        (v: string) => !!v || 'Description is required',
        (v: string) => v.length > 10 || 'More than ten letters required'
    ],
    end_date: [
        (v: string) => !!v || 'End Date is required',
        // (v: string) => v.length > 10 || 'More than ten letters required'
    ],
    start_date: [
        (v: string) => !!v || 'Start Date is required',
        // (v: string) => v.length > 10 || 'More than ten letters required'
    ],
})

// const sendFindings = async (e: any) => {
//     e.preventDefault();

//     try {
//         setLoading(true)

//         const values = { ...stepFiveFields.value }


//         let objectValues = {
//             "organization_id": getActiveOrg.value?.uuid,
//             "permit_id": getAcceptedPermit.value?.uuid,
//             "detail":  values?.detail,
//         }

//         const resp = await permitToWorkStore.sendFindings(objectValues)
//             .catch((error: any) => {
//                 console.log(error)
//                 throw error
//             })
//             .then((resp: any) => {
//                 return resp
//             });

//         if (resp?.message == 'success') {
//             setLoading(false)
//             setViewFindingDialog(false)
//             permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
//         }

//         setLoading(false)
//         setViewFindingDialog(false)
//         stepFiveFields.value.detail = ''
//     } catch (error) {
//         console.log(error)
//         setLoading(false)           
//          setViewFindingDialog(false)
//     }


// }
// const sendRecommendation = async (e: any) => {
//     e.preventDefault();

//     try {
//         setLoading(true)

//         const values = { ...stepFiveFields.value }

//         let objectValues = {
//             "organization_id": getActiveOrg.value?.uuid,
//             "permit_id": getAcceptedPermit.value?.uuid,
//             "title": values?.title,
//             "description": values?.description,
//             "assignee_id": values?.assigneeId,
//             "start_date": moment(values?.start_date).format('YYYY-MM-DD HH:mm:ss'),
//             "end_date":  moment(values?.end_date).format('YYYY-MM-DD HH:mm:ss'),  
//             "priority_id": values?.priorityId
//         }


//         const resp = await permitToWorkStore.sendRecommendation(objectValues)
//             .catch((error: any) => {
//                 console.log(error)
//                 throw error
//             })
//             .then((resp: any) => {
//                 return resp
//             });


//         if (resp?.message == 'success') {
//             setLoading(false)
//             setViewRecommendationDialog(false)
//             permitToWorkStore.getAcceptedPermit(route.params.permit_id as string);
//         }

//         setLoading(false)
//         setViewRecommendationDialog(false)



//     } catch (error) {
//         console.log(error)
//         setLoading(false)
//         setViewRecommendationDialog(false)
//     }

// }


// const completeInspection = async (member: any) => {

//     try {
//         setLoading(true)
        
//         let objectValues = {
//             "organization_id": getActiveOrg.value?.uuid,
//             "permit_id": getAcceptedPermit.value?.uuid,
//         }
        
//         Swal.fire({
//             title: 'Info!',
//             text: 'Do you want to close inspection?',
//             icon: 'info',
//             confirmButtonText: 'Yes',
//             cancelButtonText: 'No',
//             showCancelButton: true,
//             allowOutsideClick: false,
//         }).then((result) => {
//             if (result.isConfirmed) {
                
                
//                 permitToWorkStore.completeInspection(objectValues)
//                     .catch((error: any) => {
//                         throw error
//                     })
//                     .then((resp: any) => {
//                         //  investigationStore.getInvestigation(route.params.observation_id as string);
//                         // 
//                         if(resp.message == 'success') {
//                             router.push(`/inspections`)
//                         }
//                         // return resp
//                     });


//             }
//         });

        
            


//         setLoading(false)

//     } catch (error) {
//         console.log(error)
//         setLoading(false)
//     }

// }


//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------------------------

// ---------------------------------------------------

const thankyou = ref(false);

const tab = ref('tab-1');
const checkbox = ref([]);

function changeTab(e: string) {
    tab.value = e;
}
const blankFn = () => {
    console.log('black')
}
const goToRoute = (value : string) => {
    router.push(value)
}


</script>

<template>
    <div>
        <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs"></BaseBreadcrumb>


        <v-row v-if="getAcceptedPermit?.is_request_accepted">
            <v-col cols="12" md="11">

                <UiParentCard variant="outlined">
                    <v-card-text>
                        <h1 class="text-uppercase mb-3"> Job Title - {{ getAcceptedPermit?.job_title }}
                        </h1>
                        <h2 class="text-uppercase mb-3">Organization - {{
                            getAcceptedPermit?.issuer_organization?.name }}</h2>
                    </v-card-text>
                    <v-card-text>
                        <v-tabs v-model="tab" color="primary" class="customTab">
                            <v-tab value="tab-1" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <UsersIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Basic Permit</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">Details</span>
                                </div>
                            </v-tab>

                            <v-tab value="tab-2" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <UsersIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Build Team</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">Members</span>
                                </div>
                            </v-tab>


                            <v-tab value="tab-3" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Send Job Hazard Analysis</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        To Issuer
                                    </span>
                                </div>
                            </v-tab>
                            <v-tab value="tab-4" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Declaration and Approval</div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        Of Permit
                                    </span>
                                </div>
                            </v-tab>
                            <v-tab value="tab-5" rounded="md" class="mb-3 mx-2 text-left" height="70">
                                <CreditCardIcon stroke-width="1.5" width="20" class="v-icon--start" />
                                <div>
                                    <div>Request and Completion </div>
                                    <span
                                        class="text-subtitle-2 text-lightText text-medium-emphasis font-weight-medium d-block">
                                        Of Job
                                    </span>
                                </div>
                            </v-tab>
                        </v-tabs>
                        <v-window v-model="tab" class="customTab">
                            <v-window-item value="tab-1" class="pa-1">

                                <div>
                                    <v-row class="mt-3 px-4">

                                        <v-col cols="12">

                                            <v-btn color="primary" @click="setUpdateInspectionDialog(true)" class="mr-2"
                                                v-if="isLoggedInUserIsPermitHolder && !getAcceptedPermit?.is_rf_accepted">Update
                                                Detail</v-btn>
                                                <v-btn color="primary" @click="setActionRequestFormDialog(true)" class="mr-2"
                                                    v-if="isLoggedInUserIsPermitIssuer  && !getAcceptedPermit?.is_rf_accepted">Review Permit Request Form</v-btn>
                                            <v-sheet>
                                                <v-dialog v-model="actionRequestFormDialog" max-width="800">
                                                    <v-card>


                                                        <v-card-text>
                                                            <div class="d-flex justify-space-between">
                                                                <h3 class="text-h3">Review Permit Detail </h3>
                                                                <v-btn icon @click="setActionRequestFormDialog(false)"
                                                                    size="small" flat>
                                                                    <XIcon size="16" />
                                                                </v-btn>
                                                            </div>
                                                        </v-card-text>
                                                        <v-divider></v-divider>

                                                        <v-card-text>
                                                            <VForm v-model="valid" ref="formContainer" fast-fail
                                                                lazy-validation @submit.prevent="actionRequestForm"
                                                                class="py-1">
                                                                <VRow class="mt-5 mb-3">

                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="font-weight-medium pb-1">Action</v-label>
                                                                        <VSelect v-model="requestFormField.status"
                                                                            :items="[{ 'name': 'accepted', 'description': 'Accept Request Form' }, { 'name': 'action_required', 'description': 'Requires Action' }]"
                                                                            label="Select" single-line
                                                                            variant="outlined" class="text-capitalize"
                                                                            :rules="[(v: any) => !!v || 'You must select to continue!']"
                                                                            item-title='description' item-value="name"
                                                                            required>
                                                                        </VSelect>
                                                                    </VCol>
                                                                    
                                                                    
                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Comment</v-label>
                                                                        <VTextarea variant="outlined" outlined
                                                                            name="Comment" label="Comment"
                                                                            v-model="requestFormField.comment"
                                                                            required
                                                                            :rules="[(v: any) => !!v || 'Field is required!']"
                                                                            :color="requestFormField.comment.length > 10 ? 'success' : 'primary'">
                                                                        </VTextarea>
                                                                    </VCol>





                                                                    <VCol cols="12" lg="12" class="text-right">

                                                                        <v-btn color="error"
                                                                            @click="setActionRequestFormDialog(false)"
                                                                            variant="text">Cancel</v-btn>

                                                                        <v-btn color="primary" type="submit"
                                                                            :loading="loading" :disabled="!valid"
                                                                            @click="actionRequestForm">
                                                                            <span v-if="!loading">
                                                                                Send Response
                                                                            </span>
                                                                            <clip-loader v-else :loading="loading"
                                                                                color="white"
                                                                                :size="'25px'"></clip-loader>
                                                                        </v-btn>

                                                                    </VCol>
                                                                </VRow>
                                                            </VForm>

                                                        </v-card-text>
                                                    </v-card>
                                                </v-dialog>
                                                <v-dialog v-model="updateInspectionDialog" max-width="800">
                                                    <v-card>


                                                        <v-card-text>

                                                            <div class="d-flex justify-space-between">
                                                                <h3 class="text-h3">Update Inspection Details</h3>
                                                                <v-btn icon @click="setUpdateInspectionDialog(false)"
                                                                    size="small" flat>
                                                                    <XIcon size="16" />
                                                                </v-btn>
                                                            </div>

                                                        </v-card-text>
                                                        <v-divider></v-divider>

                                                        <v-card-text>

                                                            <VForm v-model="valid" ref="formContainer" fast-fail
                                                                lazy-validation @submit.prevent="updatePermitDetails"
                                                                class="py-1">
                                                                <VRow class="mt-5 mb-3">

                                                                    <VCol cols="12" md="6">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Name
                                                                            Of
                                                                            Job</v-label>
                                                                        <VTextField type="text"
                                                                            v-model="stepDetailFields.jobName"
                                                                            :rules="stepDetailFieldRules.jobName"
                                                                            required variant="outlined" label=""
                                                                            :color="stepDetailFields.jobName.length > 2 ? 'success' : 'primary'">
                                                                        </VTextField>
                                                                    </VCol>
                                                                    <VCol cols="12" md="6">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Job
                                                                            Code/Job Order
                                                                            No</v-label>
                                                                        <VTextField type="text"
                                                                            v-model="stepDetailFields.jobCode"
                                                                            :rules="stepDetailFieldRules.jobCode"
                                                                            required variant="outlined" label=""
                                                                            :color="stepDetailFields.jobCode.length > 2 ? 'success' : 'primary'">
                                                                        </VTextField>
                                                                    </VCol>
                                                                    <VCol cols="12" md="6">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Job
                                                                            Location Name</v-label>
                                                                        <VTextField type="text"
                                                                            v-model="stepDetailFields.locationName"
                                                                            :rules="stepDetailFieldRules.locationName"
                                                                            required variant="outlined" label=""
                                                                            :color="stepDetailFields.locationName.length > 2 ? 'success' : 'primary'">
                                                                        </VTextField>
                                                                    </VCol>
                                                                    <VCol cols="12" md="6">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Job
                                                                            Location Number</v-label>
                                                                        <VTextField type="text"
                                                                            v-model="stepDetailFields.locationNumber"
                                                                            :rules="stepDetailFieldRules.locationNumber"
                                                                            required variant="outlined" label=""
                                                                            :color="stepDetailFields.locationNumber.length > 2 ? 'success' : 'primary'">
                                                                        </VTextField>
                                                                    </VCol>
                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Name
                                                                            Of Contractor/
                                                                            Project Team</v-label>
                                                                        <VTextField type="text"
                                                                            v-model="stepDetailFields.contractorName"
                                                                            :rules="stepDetailFieldRules.contractorName"
                                                                            required variant="outlined" label=""
                                                                            :color="stepDetailFields.contractorName.length > 2 ? 'success' : 'primary'">
                                                                        </VTextField>
                                                                    </VCol>

                                                                    <VCol cols="12" md="6">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Work
                                                                            Start
                                                                            Date</v-label>
                                                                        <VueDatePicker
                                                                            input-class-name="dp-custom-input"
                                                                            v-model="stepDetailFields.start_date"
                                                                            :min-date="new Date()" required>
                                                                        </VueDatePicker>
                                                                    </VCol>
                                                                    <VCol cols="12" md="6">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Work
                                                                            End
                                                                            Date</v-label>
                                                                        <VueDatePicker
                                                                            input-class-name="dp-custom-input"
                                                                            :disabled='!stepDetailFields.start_date'
                                                                            v-model="stepDetailFields.end_date"
                                                                            :min-date="stepDetailFields.start_date ? new Date(stepDetailFields.start_date) : new Date()"
                                                                            required></VueDatePicker>
                                                                    </VCol>

                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Request
                                                                            Date</v-label>
                                                                        <VTextField type="hidden"
                                                                            v-model="stepDetailFields.requestDate"
                                                                            :rules="stepDetailFieldRules.requestDate"
                                                                            required variant="outlined" label=""
                                                                            :color="stepDetailFields.requestDate.length > 2 ? 'success' : 'primary'">
                                                                        </VTextField>
                                                                        <VueDatePicker
                                                                            input-class-name="dp-custom-input"
                                                                            v-model="stepDetailFields.requestDate"
                                                                            :min-date="new Date()" required>
                                                                        </VueDatePicker>
                                                                    </VCol>


                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Description
                                                                            Of
                                                                            Facility</v-label>
                                                                        <VTextarea type="text"
                                                                            v-model="stepDetailFields.description"
                                                                            :rules="stepDetailFieldRules.description"
                                                                            required variant="outlined" label=""
                                                                            :color="stepDetailFields.description.length > 2 ? 'success' : 'primary'">
                                                                        </VTextarea>
                                                                    </VCol>

                                                                    <VCol cols="12" lg="12" class="text-right">
                                                                        <v-btn color="error"
                                                                            @click="setUpdateInspectionDialog(false)"
                                                                            variant="text">Cancel</v-btn>

                                                                        <v-btn color="primary" type="submit"
                                                                            :loading="loading" :disabled="!valid"
                                                                            @click="updatePermitDetails">
                                                                            <span v-if="!loading">
                                                                                Update
                                                                            </span>
                                                                            <clip-loader v-else :loading="loading"
                                                                                color="white"
                                                                                :size="'25px'"></clip-loader>
                                                                        </v-btn>

                                                                    </VCol>
                                                                </VRow>
                                                            </VForm>
                                                        </v-card-text>
                                                    </v-card>
                                                </v-dialog>
                                            </v-sheet>
                                        </v-col>

                                        <v-col cols="12">
                                            <v-table>
                                                <thead>
                                                    <tr>
                                                        <th class="text-left">

                                                        </th>
                                                        <th class="text-left">

                                                        </th>
                                                        <th class="text-left">

                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <template v-if="!getAcceptedPermit?.is_rf_pending">
                                                        <tr>
                                                            <td></td>
                                                            <td>Issuer Status</td>
                                                            <td>{{ `${getAcceptedPermit?.is_rf_accepted ? "Accepted" : "Update Required"}` }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>Issuer Comment</td>
                                                            <td>{{ `${getAcceptedPermit?.request_form_comment}` }}</td>
                                                        </tr>
                                                    </template>
                                                    <template v-if="getAcceptedPermit">
                                                        <tr>
                                                            <td></td>
                                                            <td>Job Title</td>
                                                            <td>{{ `${getAcceptedPermit?.job_title}` }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>Job Code</td>
                                                            <td>{{ `${getAcceptedPermit?.job_code}` }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>Name of Contractor/Project Team</td>
                                                            <td>{{ `${getAcceptedPermit?.contractor_name}` }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>Location ID</td>
                                                            <td>{{ `${getAcceptedPermit?.location_name}` }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>Location Number</td>
                                                            <td>{{ `${getAcceptedPermit?.location_id_no}` }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>Permit Request Type</td>
                                                            <td>{{ `${getAcceptedPermit?.request_type?.description}` }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>Estimated Duration</td>
                                                            <td>{{ `${getAcceptedPermit?.estimated_duration}` }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>Request Date</td>
                                                            <td>{{ `${getAcceptedPermit?.requested_date}` }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>Work Start Date</td>
                                                            <td>{{ `${getAcceptedPermit?.work_start_time}` }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>Work End Date</td>
                                                            <td>{{ `${getAcceptedPermit?.work_end_time}` }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td>Permit Status</td>
                                                            <td>{{ `${getAcceptedPermit?.status}` }}</td>
                                                        </tr>
                                                    </template>

                                                </tbody>
                                            </v-table>
                                        </v-col>
                                    </v-row>

                                </div>


                                <v-row class="mt-3">
                                    <v-col cols="12" sm="6">

                                    </v-col>
                                    <v-col cols="12" sm="6" class="text-sm-right">
                                        <v-btn color="primary" @click="changeTab('tab-2')"
                                            v-if="isLoggedInUserIsPermitIssuer || isLoggedInUserIsPermitHolder">Next
                                            Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                            <v-window-item value="tab-2" class="pa-1">

                                <div>
                                    <v-row class="mt-3 px-4">

                                        <v-col cols="12">

                                            <v-btn color="primary" @click="setAddMembersDialog(true)" class="mr-2"
                                                v-if="isLoggedInUserIsPermitHolder  && !getAcceptedPermit?.is_declaration_stage">Add Members</v-btn>
                                            <v-sheet>
                                                <v-dialog v-model="addMembersDialog" max-width="800">
                                                    <v-card>
                                                        <v-card-text>

                                                            <div class="d-flex justify-space-between">
                                                                <h3 class="text-h3">Add Members</h3>
                                                                <v-btn icon @click="setAddMembersDialog(false)"
                                                                    size="small" flat>
                                                                    <XIcon size="16" />
                                                                </v-btn>
                                                            </div>

                                                        </v-card-text>
                                                        <v-divider></v-divider>

                                                        <v-card-text>

                                                            <VForm v-model="valid" ref="formContainer" fast-fail
                                                                lazy-validation @submit.prevent="sendTeamMembers"
                                                                class="py-1">
                                                                <VRow class="mt-5 mb-3">

                                                                    <VCol cols="12" md="12">
                                                                        <v-label class="font-weight-medium pb-1">Select
                                                                            Supervisor/ Entry Supervisor</v-label>
                                                                        <VSelect v-model="fields.supervisor"
                                                                            :items="getHoldingMembers"
                                                                            @update:modelValue="clearOtherUsers"
                                                                            :rules="fieldRules.supervisor"
                                                                            label="Select" item-title="lastName"
                                                                            item-value="id"
                                                                            :item-props="(item: any) => ({ title: `${item?.lastName} ${item?.firstName}`, subtitle: `${item?.email}` })"
                                                                            single-line variant="outlined"
                                                                            class="text-capitalize">
                                                                        </VSelect>
                                                                    </VCol>
                                                                    <template
                                                                        v-if="getAcceptedPermit?.request_type?.is_confined_space && fields.supervisor">

                                                                        <VCol cols="12" md="12">
                                                                            <v-label
                                                                                class="font-weight-medium pb-1">Select
                                                                                Entrants </v-label>
                                                                            <VSelect v-model="fields.entrants"
                                                                                :items="filteredSupervisor"
                                                                                label="Select"
                                                                                :rules="[(v: any) => !!v || 'You must select to continue!']"
                                                                                item-title="lastName" item-value="id"
                                                                                :item-props="(item: any) => ({ title: `${item?.lastName} ${item?.firstName}`, subtitle: `${item?.email}` })"
                                                                                single-line variant="outlined"
                                                                                class="text-capitalize" multiple chips>
                                                                            </VSelect>
                                                                        </VCol>
                                                                        <VCol cols="12" md="12">
                                                                            <v-label
                                                                                class="font-weight-medium pb-1">Select
                                                                                Attendant </v-label>
                                                                            <VSelect v-model="fields.attendants"
                                                                                :items="filteredSupervisor"
                                                                                label="Select"
                                                                                :rules="[(v: any) => !!v || 'You must select to continue!']"
                                                                                item-title="lastName" item-value="id"
                                                                                :item-props="(item: any) => ({ title: `${item?.lastName} ${item?.firstName}`, subtitle: `${item?.email}` })"
                                                                                single-line variant="outlined"
                                                                                class="text-capitalize" multiple chips>
                                                                            </VSelect>
                                                                        </VCol>
                                                                    </template>


                                                                    <template
                                                                        v-if="!getAcceptedPermit?.request_type?.is_confined_space && fields.supervisor">

                                                                        <VCol cols="12" md="12">
                                                                            <v-label
                                                                                class="font-weight-medium pb-1">Select
                                                                                HSE officers </v-label>
                                                                            <VSelect v-model="fields.officers"
                                                                                :items="filteredSupervisor"
                                                                                label="Select"
                                                                                :rules="[(v: any) => !!v || 'You must select to continue!']"
                                                                                item-title="lastName" item-value="id"
                                                                                :item-props="(item: any) => ({ title: `${item?.lastName} ${item?.firstName}`, subtitle: `${item?.email}` })"
                                                                                single-line variant="outlined"
                                                                                class="text-capitalize" multiple chips>
                                                                            </VSelect>
                                                                        </VCol>

                                                                        <VCol cols="12" md="12">
                                                                            <v-label
                                                                                class="font-weight-medium pb-1">Select
                                                                                Site Nurse/First Aider</v-label>
                                                                            <VSelect v-model="fields.nurses"
                                                                                :items="filteredSupervisor"
                                                                                label="Select" item-title="lastName"
                                                                                item-value="id"
                                                                                :item-props="(item: any) => ({ title: `${item?.lastName} ${item?.firstName}`, subtitle: `${item?.email}` })"
                                                                                single-line variant="outlined"
                                                                                class="text-capitalize" multiple chips>
                                                                            </VSelect>
                                                                        </VCol>

                                                                        <VCol cols="12" md="12">
                                                                            <v-label
                                                                                class="font-weight-medium pb-1">Select
                                                                                Workers </v-label>
                                                                            <VSelect v-model="fields.workers"
                                                                                :items="filteredSupervisor"
                                                                                label="Select"
                                                                                :rules="[(v: any) => !!v || 'You must select to continue!']"
                                                                                item-title="lastName" item-value="id"
                                                                                :item-props="(item: any) => ({ title: `${item?.lastName} ${item?.firstName}`, subtitle: `${item?.email}` })"
                                                                                single-line variant="outlined"
                                                                                class="text-capitalize" multiple chips>
                                                                            </VSelect>
                                                                        </VCol>
                                                                    </template>




                                                                    <VCol cols="12" lg="12" class="text-right">
                                                                        <v-btn color="error"
                                                                            @click="setAddMembersDialog(false)"
                                                                            variant="text">Cancel</v-btn>

                                                                        <v-btn color="primary" type="submit"
                                                                            :loading="loading" :disabled="!valid"
                                                                            @click="sendTeamMembers">
                                                                            <span v-if="!loading">
                                                                                Save
                                                                            </span>
                                                                            <clip-loader v-else :loading="loading"
                                                                                color="white"
                                                                                :size="'25px'"></clip-loader>
                                                                        </v-btn>

                                                                    </VCol>
                                                                </VRow>
                                                            </VForm>
                                                        </v-card-text>
                                                    </v-card>
                                                </v-dialog>
                                            </v-sheet>
                                        </v-col>

                                        <v-col cols="12">
                                            <v-table>
                                                <thead>
                                                    <tr>
                                                        <th class="text-left">
                                                            #
                                                        </th>
                                                        <th class="text-left">
                                                            Full Name
                                                        </th>
                                                        <th class="text-left">
                                                            Email
                                                        </th>
                                                        <th class="text-left">
                                                            Position
                                                        </th>
                                                        <th class="text-left">
                                                            Action
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <tr v-for="(member, index) in getMembers" :key="member">
                                                        <td>{{ computedIndex(index) }}</td>
                                                        <td>{{ `${member?.member?.lastName}
                                                            ${member?.member?.firstName}` }}</td>
                                                        <td>{{ `${member?.member?.email}` }}</td>
                                                        <td>{{ `${member?.position.split('_').join(' ')}` }}</td>
                                                        <td>
                                                            <v-btn color='error' size='small'
                                                                v-if="isLoggedInUserIsPermitHolder  && !getAcceptedPermit?.is_declaration_stage"
                                                                @click="removeMember(member?.member?.id)">
                                                                Remove
                                                            </v-btn>
                                                        </td>
                                                    </tr>


                                                </tbody>
                                            </v-table>
                                        </v-col>
                                    </v-row>

                                </div>
                                <v-row class="mt-3">
                                    <v-col cols="12" sm="6">

                                    </v-col>
                                    <v-col cols="12" sm="6" class="text-sm-right">
                                        <v-btn color="primary" @click="changeTab('tab-2')"
                                            v-if="isLoggedInUserIsPermitIssuer || isLoggedInUserIsPermitHolder">Next
                                            Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                            <v-window-item value="tab-3" class="pa-1">

                                <div>
                                    <v-row class="mt-3 px-4">
                                        <v-col cols="12">

                                            <template v-if="!getAcceptedPermit?.is_jha_accepted">
                                                <v-btn color="primary" @click="setSendJHADialog(true)" class="mr-2"
                                                    v-if="isLoggedInUserIsPermitHolder">Select Job Hazard Analysis</v-btn>
                                            </template>
                                            
                                            <v-sheet>
                                                <v-dialog v-model="sendJHADialog" max-width="800">
                                                    <v-card>


                                                        <v-card-text>
                                                            <div class="d-flex justify-space-between">
                                                                <h3 class="text-h3">Select From Approved JHA </h3>
                                                                <v-btn icon @click="setSendJHADialog(false)"
                                                                    size="small" flat>
                                                                    <XIcon size="16" />
                                                                </v-btn>
                                                            </div>
                                                        </v-card-text>
                                                        <v-divider></v-divider>

                                                        <v-card-text>
                                                            <VForm v-model="valid" ref="formContainer" fast-fail
                                                                lazy-validation @submit.prevent="sendJHA"
                                                                class="py-1">
                                                                <VRow class="mt-5 mb-3">

                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="font-weight-medium pb-1">Approved Job Hazard Analysis</v-label>
                                                                        <VSelect v-model="stepThreeFields.jha_id"
                                                                            :items="getActiveJHA"
                                                                            label="Select" single-line
                                                                            variant="outlined" class="text-capitalize"
                                                                            :rules="[(v: any) => !!v || 'You must select to continue!']"
                                                                            item-title='title' item-value="id"
                                                                            required>
                                                                        </VSelect>
                                                                    </VCol>
                                                                    <!-- <template
                                                                        v-if="stepThreeFields.status == 'declined'">
                                                                        <VCol cols="12" md="12">
                                                                            <v-label
                                                                                class="font-weight-medium pb-1">Suggest
                                                                                Time</v-label>
                                                                            <VueDatePicker
                                                                                input-class-name="dp-custom-input"
                                                                                v-model="stepThreeFields.date_time"
                                                                                :min-date="new Date()" required>
                                                                            </VueDatePicker>
                                                                        </VCol>

                                                                        <VCol cols="12" md="12">
                                                                            <v-label
                                                                                class="text-subtitle-1 font-weight-medium pb-1">Comment</v-label>
                                                                            <VTextarea variant="outlined" outlined
                                                                                name="Comment" label="Comment"
                                                                                v-model="stepThreeFields.comment"
                                                                                required
                                                                                :color="stepThreeFields.comment.length > 10 ? 'success' : 'primary'">
                                                                            </VTextarea>
                                                                        </VCol>


                                                                    </template> -->



                                                                    <VCol cols="12" lg="12" class="text-right">

                                                                        <v-btn color="error"
                                                                            @click="setSendJHADialog(false)"
                                                                            variant="text">Cancel</v-btn>

                                                                        <v-btn color="primary" type="submit"
                                                                            :loading="loading" :disabled="!valid"
                                                                            @click="sendJHA">
                                                                            <span v-if="!loading">
                                                                                Send for Approval
                                                                            </span>
                                                                            <clip-loader v-else :loading="loading"
                                                                                color="white"
                                                                                :size="'25px'"></clip-loader>
                                                                        </v-btn>

                                                                    </VCol>
                                                                </VRow>
                                                            </VForm>

                                                        </v-card-text>
                                                    </v-card>
                                                </v-dialog>
                                                
                                                <v-dialog v-model="actionJHADialog" max-width="800">
                                                    <v-card>


                                                        <v-card-text>
                                                            <div class="d-flex justify-space-between">
                                                                <h3 class="text-h3">Action JHA </h3>
                                                                <v-btn icon @click="setActionJHADialog(false)"
                                                                    size="small" flat>
                                                                    <XIcon size="16" />
                                                                </v-btn>
                                                            </div>
                                                        </v-card-text>
                                                        <v-divider></v-divider>

                                                        <v-card-text>
                                                            <VForm v-model="valid" ref="formContainer" fast-fail
                                                                lazy-validation @submit.prevent="actionJHA"
                                                                class="py-1">
                                                                <VRow class="mt-5 mb-3">

                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="font-weight-medium pb-1">Action</v-label>
                                                                        <VSelect v-model="stepThreeFields.status"
                                                                            :items="[{ 'name': 'accepted', 'description': 'Accept JHA' }, { 'name': 'declined', 'description': 'Decline JHA' }]"
                                                                            label="Select" single-line
                                                                            variant="outlined" class="text-capitalize"
                                                                            :rules="[(v: any) => !!v || 'You must select to continue!']"
                                                                            item-title='description' item-value="name"
                                                                            required>
                                                                        </VSelect>
                                                                    </VCol>
                                                                    
                                                                    
                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Comment</v-label>
                                                                        <VTextarea variant="outlined" outlined
                                                                            name="Comment" label="Comment"
                                                                            v-model="stepThreeFields.comment"
                                                                            required
                                                                            :color="stepThreeFields.comment.length > 10 ? 'success' : 'primary'">
                                                                        </VTextarea>
                                                                    </VCol>





                                                                    <VCol cols="12" lg="12" class="text-right">

                                                                        <v-btn color="error"
                                                                            @click="setActionJHADialog(false)"
                                                                            variant="text">Cancel</v-btn>

                                                                        <v-btn color="primary" type="submit"
                                                                            :loading="loading" :disabled="!valid"
                                                                            @click="actionJHA">
                                                                            <span v-if="!loading">
                                                                                Send Response
                                                                            </span>
                                                                            <clip-loader v-else :loading="loading"
                                                                                color="white"
                                                                                :size="'25px'"></clip-loader>
                                                                        </v-btn>

                                                                    </VCol>
                                                                </VRow>
                                                            </VForm>

                                                        </v-card-text>
                                                    </v-card>
                                                </v-dialog>
                                            </v-sheet>
                                        </v-col>

                                        <v-col cols="12">
                                            <v-table>
                                                <thead>
                                                    <tr>

                                                        <th class="text-left">
                                                            #
                                                        </th>
                                                        <th class="text-left">
                                                            Job Title
                                                        </th>
                                                        <th class="text-left">
                                                            Review Status
                                                        </th>
                                                        <th class="text-left">
                                                            Reviewer
                                                        </th>
                                                        <th class="text-left">
                                                            Comment
                                                        </th>
                                                        <th class="text-left">
                                                            Date Reviewed
                                                        </th>
                                                        <th class="text-left">
                                                            Status
                                                        </th>
                                                        <th class="text-left">
                                                            Action
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-if="getJHA">
                                                        <td>{{ ''}}</td>
                                                        <td>
                                                            {{ `${getJHA?.title ? getJHA?.title
                                                            : ""}` }}

                                                        </td>
                                                        <td>
                                                            {{ `${getJHA?.status ?
                                                            getJHA?.status : ""}` }}

                                                        </td>
                                                        <td>
                                                            {{ `${getJHA?.reviewer_user?.full_name ?
                                                            getJHA?.reviewer_user?.full_name : ""}` }}
                                                        </td>
                                                        <td>
                                                            {{ `${getJHA?.status_message ?
                                                            getJHA?.status_message : ""}` }}
                                                        </td>
                                                        <td>
                                                            {{ `${getJHA?.reviewed_date ?
                                                            getJHA?.reviewed_date : ""}` }}
                                                        </td>
                                                        <td>
                                                            {{ `${getAcceptedPermit?.jha_status ?
                                                            getAcceptedPermit?.jha_status : ""}` }}
                                                        </td>
                                                        <td>
                                                            <template v-if="isLoggedInUserIsPermitIssuer">
                                                                <v-btn color='primary' size='small' class="mr-2"
                                                                    @click="setActionJHADialog(true, 'accepted')"
                                                                    v-if="isLoggedInUserIsPermitHolder && getAcceptedPermit?.is_jha_pending ">
                                                                    Accept
                                                                </v-btn>
                                                                <v-btn color='error' size='small' class="mr-2"
                                                                    @click="setActionJHADialog(true, 'declined')"
                                                                    v-if="isLoggedInUserIsPermitHolder && getAcceptedPermit?.is_jha_pending ">
                                                                    Decline
                                                                </v-btn>
                                                            </template>
                                                                <v-btn color='primary' size='small' class="mr-2"
                                                                    @click="goToRoute(`/review-hazard-analysis/${getJHA?.uuid}`)">
                                                                    Review JHA
                                                                </v-btn>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </v-table>
                                        </v-col>
                                    </v-row>

                                </div>
                                <v-row class="mt-3">
                                    <v-col cols="6">
                                        <v-btn color="primary" variant="tonal" @click="changeTab('tab-2')">Back</v-btn>
                                    </v-col>
                                    <v-col cols="6" class="text-right">
                                        <v-btn color="primary" @click="changeTab('tab-4')"
                                            v-if="isLoggedInUserIsPermitIssuer || isLoggedInUserIsPermitHolder">Next
                                            Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                            <v-window-item value="tab-4" class="pa-1">

                                <div>
                                    <v-row class="mt-3 px-4">


                                        <v-col cols="12">

                                            <template v-if="getAcceptedPermit?.is_jha_accepted && getAcceptedPermit?.is_rf_accepted  && !getAcceptedPermit?.is_declaration_stage">
                                                <v-btn color="primary" @click="sendForDeclaration" class="mr-2"
                                                    v-if="isLoggedInUserIsPermitIssuer">Send For Declaration</v-btn>
                                            </template>
                                            
                                            <template v-if="getAcceptedPermit?.can_issue && !getAcceptedPermit?.is_issued">
                                                <v-btn color="primary" @click="setSendIssuePermitDialog(true)" class="mr-2"
                                                    v-if="isLoggedInUserIsPermitIssuer">Issue Permit</v-btn>
                                            </template>
                                            
                                            <v-sheet>
                                                <v-dialog v-model="sendIssuePermitDialog" max-width="800">
                                                    <v-card>


                                                        <v-card-text>
                                                            <div class="d-flex justify-space-between">
                                                                <h3 class="text-h3">Issue Permit </h3>
                                                                <v-btn icon @click="setSendIssuePermitDialog(false)"
                                                                    size="small" flat>
                                                                    <XIcon size="16" />
                                                                </v-btn>
                                                            </div>
                                                        </v-card-text>
                                                        <v-divider></v-divider>

                                                        <v-card-text>
                                                            <VForm v-model="valid" ref="formContainer" fast-fail
                                                                lazy-validation @submit.prevent="sendIssuePermit"
                                                                class="py-1">
                                                                <VRow class="mt-5 mb-3">

                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Duration of Permit
                                                                            Start
                                                                            Date</v-label>
                                                                        <input type="hidden" v-model="issuePermitFields.start_date"/>
                                                                        <VueDatePicker
                                                                            input-class-name="dp-custom-input"
                                                                            v-model="issuePermitFields.start_date"
                                                                            :min-date="new Date(getAcceptedPermit?.work_start_time)" required>
                                                                        </VueDatePicker>
                                                                    </VCol>
                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Duration of Permit
                                                                            End
                                                                            Date</v-label>
                                                                        <input type="hidden" v-model="issuePermitFields.end_date"/>
                                                                        <VueDatePicker
                                                                            input-class-name="dp-custom-input"
                                                                            :disabled='!issuePermitFields.start_date'
                                                                            v-model="issuePermitFields.end_date"
                                                                            :min-date="issuePermitFields.start_date ? new Date(issuePermitFields.start_date) : new Date()"
                                                                            required></VueDatePicker>
                                                                    </VCol>



                                                                    <VCol cols="12" lg="12" class="text-right">

                                                                        <v-btn color="error"
                                                                            @click="setSendIssuePermitDialog(false)"
                                                                            variant="text">Cancel</v-btn>

                                                                        <v-btn color="primary" type="submit"
                                                                            :loading="loading" :disabled="!valid"
                                                                            @click="sendIssuePermit">
                                                                            <span v-if="!loading">
                                                                                Send Permit
                                                                            </span>
                                                                            <clip-loader v-else :loading="loading"
                                                                                color="white"
                                                                                :size="'25px'"></clip-loader>
                                                                        </v-btn>

                                                                    </VCol>
                                                                </VRow>
                                                            </VForm>

                                                        </v-card-text>
                                                    </v-card>
                                                </v-dialog>
                                                
                                            </v-sheet>
                                        </v-col>
                                        <!--  -->
                                        
                                        <v-col cols="12">
                                            <v-table>
                                                <thead>
                                                    <tr>
                                                        <th class="text-left">
                                                            #
                                                        </th>
                                                        <th class="text-left">
                                                            Full Name
                                                        </th>
                                                        <th class="text-left">
                                                            Email
                                                        </th>
                                                        <th class="text-left">
                                                            Position
                                                        </th>
                                                        <th class="text-left">
                                                            Action
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <tr v-for="(member, index) in getMembers" :key="member">
                                                        <td>{{ computedIndex(index) }}</td>
                                                        <td>{{ `${member?.member?.lastName}
                                                            ${member?.member?.firstName}` }}</td>
                                                        <td>{{ `${member?.member?.email}` }}</td>
                                                        <td>{{ `${member?.position.split('_').join(' ')}` }}</td>
                                                        <td>
                                                            <template v-if='member.is_declared'>
                                                                <v-btn color='success' size='small'>
                                                                    Declared
                                                                </v-btn>
                                                            </template>
                                                            <template v-else>
                                                                <v-btn color='primary' size='small'
                                                                    v-if="getAcceptedPermit?.is_declaration_stage && getAuthUser.id == member.member_id"
                                                                    @click="sendDeclaration">
                                                                    Send Declaration
                                                                </v-btn>
                                                            </template>
                                                        </td>
                                                    </tr>


                                                </tbody>
                                            </v-table>
                                        </v-col>

                                    </v-row>

                                </div>
                                <v-row class="mt-3">
                                    <v-col cols="6">
                                        <v-btn color="primary" variant="tonal" @click="changeTab('tab-3')">Back</v-btn>
                                    </v-col>
                                    <v-col cols="6" class="text-right">
                                        <v-btn color="primary" @click="changeTab('tab-5')"
                                            v-if="isLoggedInUserIsPermitIssuer || isLoggedInUserIsPermitHolder">Next
                                            Step</v-btn>
                                    </v-col>
                                </v-row>
                            </v-window-item>
                            <v-window-item value="tab-5" class="pa-1">
                                <div>
                                    <v-row class="mt-3 px-4">


                                        <v-col cols="12">
                                            <template v-if="getAcceptedPermit.is_active">
                                                <!-- Holder TO make as complete -->
                                                <v-btn color="primary" @click="setViewFindingDialog(true)" class="mr-2">Mark As Completed</v-btn>   
                                                <!-- Issuer to Review and add final comment -->
                                                <v-btn color="primary" @click="setViewFindingDialog(true)" class="mr-2">Review And Complete</v-btn>  
                                                                                          <!-- Issuer can Cancel Permit  -->
                                                <v-btn color="primary" @click="setViewFindingDialog(true)" class="mr-2">Cancel Permit</v-btn>     
                                                                                          <!-- Holder can Request to extend Permit  -->
                                                <v-btn color="primary" @click="setViewFindingDialog(true)" class="mr-2">Permit Extension Request</v-btn>  
                                                                                          <!-- Issuer can Cancel Permit  -->
                                                <v-btn color="primary" @click="setViewFindingDialog(true)" class="mr-2">Extend Permit</v-btn>   
                                                                                          <!-- Issuer can Suspend Permit  -->
                                                <v-btn color="primary" @click="setViewFindingDialog(true)" class="mr-2">Suspend Permit</v-btn>     
                                                                                          <!-- Holder can Request to extend Permit  -->
                                                <v-btn color="primary" @click="setViewFindingDialog(true)" class="mr-2">Reactivation Request </v-btn>  
                                                                                          <!-- Issuer can Reactivate Permit  -->
                                                <v-btn color="primary" @click="setViewFindingDialog(true)" class="mr-2">Reactivate Permit</v-btn>                                            
                                            </template>


                                            <v-sheet>
                                                <v-dialog v-model="viewFindingDialog" max-width="700">
                                                    <v-card>

                                                        <v-card-text>
                                                            <div class="d-flex justify-space-between">
                                                                <h3 class="text-h3">Add </h3>
                                                                <v-btn icon @click="setViewFindingDialog(false)"
                                                                    size="small" flat>
                                                                    <XIcon size="16" />
                                                                </v-btn>
                                                            </div>
                                                        </v-card-text>
                                                        <v-divider></v-divider>

                                                        <v-card-text>

                                                            <!-- <VForm v-model="valid" ref="formContainer" fast-fail
                                                                lazy-validation @submit.prevent="sendFindings"
                                                                class="py-1">
                                                                <VRow class="mt-5 mb-3">

                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Details</v-label>
                                                                        <VTextarea variant="outlined" outlined
                                                                            name="Description" label="Details"
                                                                            v-model="stepFiveFields.detail"
                                                                            :rules="stepFiveFieldRules.detail" required
                                                                            :color="stepFiveFields.detail.length > 10 ? 'success' : 'primary'">
                                                                        </VTextarea>
                                                                    </VCol>

                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="font-weight-medium pb-1">File</v-label>

                                                                        <v-file-input :show-size="1000"
                                                                            color="deep-purple-accent-4"
                                                                            label="File input"
                                                                            placeholder="Select your files"
                                                                            prepend-icon="mdi-paperclip"
                                                                            variant="outlined" counter
                                                                            accept="image/*, .pdf"
                                                                            @change="selectImage">
                                                                            <template v-slot:selection="{ fileNames }">
                                                                                <template
                                                                                    v-for="(fileName, index) in fileNames"
                                                                                    :key="fileName">
                                                                                    <v-chip v-if="index < 2"
                                                                                        class="me-2"
                                                                                        color="deep-purple-accent-4"
                                                                                        size="small" label>
                                                                                        {{ fileName }}
                                                                                    </v-chip>

                                                                                    <span v-else-if="index === 2"
                                                                                        class="text-overline text-grey-darken-3 mx-2">
                                                                                        +{{ files.length - 2 }} File(s)
                                                                                    </span>
                                                                                </template>
                                                                            </template>
                                                                        </v-file-input>

                                                                    </VCol>
                                                                    <VCol cols="12" lg="12" class="text-right">
                                                                        <v-btn color="error"
                                                                            @click="setViewFindingDialog(false)"
                                                                            variant="text">Cancel</v-btn>

                                                                        <v-btn color="primary" type="submit"
                                                                            :loading="loading" :disabled="!valid"
                                                                            @click="sendFindings">
                                                                            <span v-if="!loading">
                                                                                Submit
                                                                            </span>
                                                                            <clip-loader v-else :loading="loading"
                                                                                color="white"
                                                                                :size="'25px'"></clip-loader>
                                                                        </v-btn>

                                                                    </VCol>
                                                                </VRow>
                                                            </VForm> -->
                                                        </v-card-text>
                                                    </v-card>
                                                </v-dialog>
                                            </v-sheet>
                                        </v-col>

                                        <!-- <v-col cols="12">
                                            <v-table>
                                                <thead>
                                                    <tr>

                                                        <th class="text-left">
                                                            Inspector
                                                        </th>
                                                        <th class="text-left">
                                                            Description
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>{{ getFindings ? `${getFindings?.user?.lastName}
                                                            ${getFindings?.user?.firstName}` : '' }}</td>
                                                        <td>{{ getFindings ? `${getFindings?.description}` : '' }}</td>
                                                    </tr>
                                                </tbody>
                                            </v-table>
                                        </v-col> -->
                                    </v-row>

                                    <v-row class="mt-3 px-4">


                                        <!-- <v-col cols="12">
                                            <v-btn color="primary" @click="setViewRecommendationDialog(true)"
                                                class="mr-2">Add Corrective Action</v-btn>

                                            <v-sheet>
                                                <v-dialog v-model="viewRecommendationDialog" max-width="500">
                                                    <v-card>

                                                        <v-card-text>
                                                            <div class="d-flex justify-space-between">
                                                                <h3 class="text-h3">Add </h3>
                                                                <v-btn icon @click="setViewRecommendationDialog(false)"
                                                                    size="small" flat>
                                                                    <XIcon size="16" />
                                                                </v-btn>
                                                            </div>
                                                        </v-card-text>
                                                        <v-divider></v-divider>

                                                        <v-card-text>

                                                            <VForm v-model="valid" ref="formContainer" fast-fail
                                                                lazy-validation @submit.prevent="sendRecommendation"
                                                                class="py-1">
                                                                <VRow class="mt-5 mb-3">

                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Title</v-label>
                                                                        <VTextField type="text"
                                                                            v-model="stepFiveFields.title"
                                                                            :rules="stepFiveFieldRules.title" required
                                                                            variant="outlined" label="Title"
                                                                            :color="stepFiveFields.title.length > 2 ? 'success' : 'primary'">
                                                                        </VTextField>
                                                                    </VCol>
                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Description</v-label>
                                                                        <VTextarea variant="outlined" outlined
                                                                            name="Description" label="Description"
                                                                            v-model="stepFiveFields.description"
                                                                            :rules="stepFiveFieldRules.description"
                                                                            required
                                                                            :color="stepFiveFields.description.length > 10 ? 'success' : 'primary'">
                                                                        </VTextarea>
                                                                    </VCol>
                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="font-weight-medium pb-1">User</v-label>


                                                                        <VSelect v-model="stepFiveFields.assigneeId"
                                                                            :items="isLoggedInUserIsPermitIssuer ? getHoldingMembers : getInspecteeMembers"
                                                                            :rules="stepFiveFieldRules.assigneeId"
                                                                            label="Select" :selected="''"
                                                                            item-title='lastName' item-value="uuid"
                                                                            single-line variant="outlined"
                                                                            class="text-capitalize"
                                                                            :item-props="(item: any) => ({title:`${item?.lastName} ${item?.firstName}`, subtitle:`${item?.email}`})">

                                                                        </VSelect>
                                                                    </VCol>
                                                                    <VCol cols="12" md="12">
                                                                        <v-label
                                                                            class="font-weight-medium pb-1">Severity</v-label>
                                                                        <VSelect v-model="stepFiveFields.priorityId"
                                                                            :items="getPriorities"
                                                                            :rules="stepFiveFieldRules.priorityId"
                                                                            label="Select" :selected="''"
                                                                            item-title="description" item-value="id"
                                                                            single-line variant="outlined"
                                                                            class="text-capitalize">
                                                                        </VSelect>
                                                                    </VCol>

                                                                    <VCol cols="12" md="6">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">Start
                                                                            Date</v-label>
                                                                        <VueDatePicker
                                                                            input-class-name="dp-custom-input"
                                                                            v-model="stepFiveFields.start_date"
                                                                            :min-date="new Date()" required>
                                                                        </VueDatePicker>
                                                                    </VCol>
                                                                    <VCol cols="12" md="6">
                                                                        <v-label
                                                                            class="text-subtitle-1 font-weight-medium pb-1">End
                                                                            Date</v-label>
                                                                        <VueDatePicker
                                                                            input-class-name="dp-custom-input"
                                                                            :disabled='!stepFiveFields.start_date'
                                                                            v-model="stepFiveFields.end_date"
                                                                            :min-date="stepFiveFields.start_date ? new Date(stepFiveFields.start_date) : new Date()"
                                                                            required></VueDatePicker>
                                                                    </VCol>

                                                                    <VCol cols="12" lg="12" class="text-right">
                                                                        <v-btn color="error" @click="dialog = false"
                                                                            variant="text">Cancel</v-btn>

                                                                        <v-btn color="primary" type="submit"
                                                                            :loading="loading" :disabled="!valid"
                                                                            @click="sendRecommendation">
                                                                            <span v-if="!loading">
                                                                                Submit
                                                                            </span>
                                                                            <clip-loader v-else :loading="loading"
                                                                                color="white"
                                                                                :size="'25px'"></clip-loader>
                                                                        </v-btn>

                                                                    </VCol>
                                                                </VRow>
                                                            </VForm>
                                                        </v-card-text>
                                                    </v-card>
                                                </v-dialog>

                                            </v-sheet>
                                        </v-col> -->

                                        <v-col cols="12">
                                            <v-table>
                                                <thead>
                                                    <tr>

                                                        <th class="text-left">
                                                            #
                                                        </th>
                                                        <th class="text-left">
                                                            Title
                                                        </th>
                                                        <th class="text-left">
                                                            Description
                                                        </th>
                                                        <th class="text-left">
                                                            Assignee
                                                        </th>
                                                        <th class="text-left">
                                                            Status
                                                        </th>
                                                        <th class="text-left">
                                                            Priority
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(actions, index) in getActions" :key="actions">
                                                        <td>{{ computedIndex(index) }}</td>
                                                        <td>{{ `${actions?.title}` }}</td>
                                                        <td>{{ `${actions?.description}` }}</td>
                                                        <td>{{ `${actions?.assignee?.lastName}
                                                            ${actions?.assignee?.firstName}` }}</td>
                                                        <td>{{ `${actions?.status}` }}</td>
                                                        <td>{{ `${actions?.priority?.description}` }}</td>
                                                    </tr>
                                                </tbody>
                                            </v-table>
                                        </v-col>
                                    </v-row>

                                </div>
                                <v-row class="mt-3">
                                    <v-col cols="6">
                                        <v-btn color="primary" variant="tonal" @click="changeTab('tab-4')">Back</v-btn>
                                    </v-col>
                                    <v-col cols="12" sm="6" class="text-sm-right">
                                        <!-- <v-btn color="primary" @click="completeInspection"
                                            v-if="isLoggedInUserIsPermitIssuer && getAcceptedPermit.is_ongoing">Close
                                            Inspection</v-btn> -->

                                    </v-col>

                                </v-row>

                            </v-window-item>
                        </v-window>
                    </v-card-text>
                </UiParentCard>

            </v-col>

        </v-row>
    </div>
</template>
<style lang="scss">
.customTab {
    min-height: 68px;
}
</style>

