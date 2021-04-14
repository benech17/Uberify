let rec rotation_d l = match l with 
  |[]->[]
  |[x]->[x]
  |h::t->let ll=rotation_d t in match ll with 
    |hh::tt->hh::(h::tt)
    |_->failwith "cas impossible" ;;