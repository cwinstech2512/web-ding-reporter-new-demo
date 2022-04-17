export function reportSave (obj, sample_pdf_path) {
    console.log("refs", obj)
    console.log("item", obj.editedItem)
    var formError = false
    Object.keys(obj.editedItem).forEach(f => {
      // if(obj.$refs[f]) {
        if(!obj.editedItem[f] || obj.editedItem[f] == '') {
          obj.editedItem[f] = 'N/A'
        }
      // }
    })
    let params = obj.editedItem
    let co = sample_pdf_path.split('_');
    let url = `api/dispatch/${co[0]}/${co[1]}` + (obj.editedItem.id === -1? '' : ('/'+obj.editedItem.dispatch_id))
    obj.$swal.fire({
      title: '確定要儲存數據?',
      showCancelButton: true,
      confirmButtonText: '確認',
      showLoaderOnConfirm: true,
      cancelButtonText: '取消',
      preConfirm: () => {
        params.nominal_size = obj.selectNominalSize.nominal_size
        params.thicknes = obj.selectThickness.thickness
        params.thickness_objs = obj.thicknesGroup_Data.length > 0? obj.thicknesGroup_Data : ''

        // params.surface_status = that.editedItem.surface_status.join(',')
        // params.exam_time = that.editedItem.exam_time.join(',')
        // params.type_of_particle_olor = that.editedItem.type_of_particle_olor.join(',')
        // params.magnetizing_methon = that.editedItem.magnetizing_methon.join(',')
        return obj.$axios
          .fetchPut(url, params)
          .then(res => {
            return res.data.data
          })
          .catch(err => {
            console.log(err)
          })
      },
      allowOutsideClick: () => !obj.$swal.isLoading()
    }).then((result) => {
      if(result.isConfirmed) {
        if(result.value.code == 1000) {
            obj.$swal.fire({
              title: "成功!",
              text: "儲存數據成功",
              type: "success",
              confirmButtonClass: "btn btn-success"
            });
            obj.closeView()
        } else {
            obj.$swal.fire({
              title: "錯誤!",
              text: "儲存數據錯誤",
              type: "error",
              confirmButtonClass: "btn btn-danger"
            });
            obj.closeView()
        }
      }
    })
    return obj
}