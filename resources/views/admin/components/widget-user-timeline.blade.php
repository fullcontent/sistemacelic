<div class="tab-pane active" id="timeline">
                <!-- The timeline -->
                <ul class="timeline timeline-inverse">
                  <!-- timeline time label -->
                
                @foreach($interacoes as $key => $h)  
                  <li class="time-label">
                        <span class="bg-red">
                          {{$key}}
                        </span>
                  </li>
                  <!-- /.timeline-label -->
                  
                  @foreach($h->groupBy('servico_id') as $servico_id => $s)
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-envelope bg-blue"></i>

                    <div class="timeline-item">
                      

                      <h3 class="timeline-header"><a href="#">Serviço: {{$servico_id}}</a></h3>
                        @foreach($s as $i)
                      <div class="timeline-body">
                        {{$i->observacoes}}<span class="time"><i class="fa fa-clock-o"></i> hora</span>
                      </div>
                      
                      @endforeach
                    </div>
                    
                  </li>
                  @endforeach
                  <!-- END timeline item -->
                  @endforeach
                  <li>
                    <i class="fa fa-clock-o bg-gray"></i>
                  </li>
                </ul>
              </div>